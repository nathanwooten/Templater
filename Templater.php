<?php

namespace nathanwooten\Templater;

use Exception;

use nathanwooten\{

	Templater\TemplaterTrait,
	Templater\TemplaterTemplate as Template,
	Templater\TemplaterVariable as Variable

};

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php';

class Templater {

	/**
	 * This is the compiled base template
	 * of the Templater, all other
	 * templates are part of this template,
	 * and it's output gets stored here
	 */

	protected $compiled;
	protected $templates = [];
	protected $variables = [];

	/**
	 * The directory in which
	 * the template files reside
	 */

	protected $templates_dir;

	/**
	 * The part of a tag that
	 * surrounds the name to
	 * make it a tag, a
	 * two count indexed array of
	 * tag start and tag close characters
	 */

	protected $delimiters = [];

	/**
	 * Determines whether exceptions
	 * are thrown or passed
	 */

	public $handle = 1;

	public function __construct( $templates_dir, array $delimiters )
	{

		try {
			if ( ! is_dir( $templates_dir ) || ! is_readable( $templates_dir ) ) {
				throw new TemplaterException( sprintf( 'Unreadable template directory, %s', $templates_dir ) );
			}
		} catch( TemplaterException $e ) {
			$this->handle( $e, 1 );
		}

		$this->templates_dir = $templates_dir;
		$this->delimiters = $delimiters;

	}

	public function setTemplate( $name, $template, array $variables = [] )
	{

		if ( $name instanceof Template ) {

			$template = $name;
			$name = $template->getName();

		} else {

			$template = new Template( $name, $template, $this );
		}

		$this->setVariables( $variables );

		$this->templates[ $name ] = $template;

		return $this;

	}

	public function getTemplate( $name = 'base', array $templates = [] ) {

		$templates = empty( $templates ) ? $this->getTemplates() : $templates;

		switch ( $name ) {

			case 'base':

				reset( $templates );
				$template = current( $templates );

				break;

			default:

				$template = isset( $templates[ $name ] ) ? $templates[ $name ] : '';

				break;

		}

		return $template;

	}

	public function setTemplates( array $templates )
	{

		foreach ( $templates as $name => $template ) {
			$this->setTemplate( $name, $template );
		}

		return $this;

	}

	public function getTemplates( $string = 0 ) {

		$templates = $this->templates;

		if ( ! $string ) {
			return $templates;
		}

		foreach ( $templates as $name => $template ) {
			$templates[ $name ] = $template();
		}

		return $templates;

	}

	public function setVariable( $name, $value = null )
	{

		if ( $name instanceof Variable ) {
			$variable = $name;
			$name = $variable->getName();
		} else {
			$variable = new Variable( $name, $value, $this );
		}

		$this->variables[ $name ] = $variable;

		return $this;

	}

	public function getVariable( $name, TemplateInterface $in = null )
	{

		$in = isset( $in ) ? $in : $this;

		return array_key_exists( $name, $in->variables ) ? $in->variables[ $name ] : null;

	}

	public function setVariables( array $vars = [] )
	{

		foreach ( $vars as $name => $value ) {
			$this->setVariable( $name, $value );
		}

		return $this;

	}

	public function getVariables()
	{

		return $this->variables;

	}

	public function compile( $template )
	{

		if ( $template instanceof Template ) {
			$template = $template->getTemplate();
		}

		$input = array_merge( $this->getVariables(), $this->getTemplates() );

		$template = $this->compileTemplate( $template, $input );

		$file = $this->getDirectory() . 'toCompile.php';
		$put = file_put_contents( $file, $template );
		if ( ! $put ) {
			return false;
		}

		foreach ( $input as $name => $obj ) {
			$input[ $name ] = $obj->get();
		}

		$templater = $this;
		extract( $input );

		ob_start();
		include $file;

		$rendered = ob_get_clean();

		$filename = 'compiled' . '.php';
		$file = $this->getDirectory() . $filename;

		$put = file_put_contents( $file, $rendered );

		return $rendered;

	}

	public function compileTemplate( $template, array $input = [], $strip = true )
	{

		if ( $template instanceof Template ) {
			$template = $template->getTemplate();
		}

		$matches = $this->match( $template );
		foreach ( $matches as $tag ) {

			$name = $this->remove( $tag );

			if ( static::hasDot( $name ) ) {
				$shortName = static::getShortName( $name );
			}  else {
				$shortName = null;
			}

			if ( isset( $shortName ) ) {
				$startName = str_replace( '.' . $shortName, '', $name );
			} else {
				$startName = $name;
			}

			if ( array_key_exists( $startName, $input ) ) {
				$match = $input[ $startName ];

				if ( $match instanceof Template ) {
					$match->setTemplate( $this->compileTemplate( $match, $input ) );
				}

				$value = $match->get( $shortName );

			} elseif ( $strip ) {

				$value = '';
			}

			$template = $this->compileReplace( $name, $value, $template );

		}

		return $template;

	}

	public function compileReplace( $name, $value, $template )
	{

		if ( ! is_string( $name ) || ! is_string( $value ) || ! is_string( $template ) ) {
			throw new Exception( 'String inputs only on this method, ' . __METHOD__ );
		}

		$replace = $this->delimit( $this->remove( $name ), [], 1 );

		$template = str_replace( $replace, $value, $template );
		return $template;

	}

	public function match( $template, $specific = null )
	{

		$expression = is_null( $specific ) ? '(.*?)' : $specific . '(.*?)';

		$regex = '/' . $this->delimit( $expression ) . '/';
		preg_match_all( $regex, $template, $match );

		$match = $match[0];

		return $match;

	}

	public function delimit( $name, $delimiters = [], $tag = 0 )
	{

		$delimiters = $this->delimiters( $delimiters, $tag );

		$delimited = $delimiters[0] . trim( $name, $delimiters[0] . $delimiters[1] ) . $delimiters[1];

		return $delimited;

	}

	public function remove( $delimited )
	{

		$name = str_replace( $this->delimiters( $this->delimiters, 1 ), '', $delimited );
		return $name;

	}

	public function delimiters( array $delimiters = [], $tag = 0 )
	{

		$delimiters = empty( $delimiters ) ? $this->delimiters : $delimiters;

		$delimiters = array_values( $delimiters );

		if ( ! $tag ) {
			$delimiters[0] = '\\' . implode( '\\', str_split( $delimiters[0] ) );
			$delimiters[1] = '\\' . implode( '\\', str_split( $delimiters[1] ) );
		}

		return $delimiters;

	}

	public function setDirectory( $type, $directory ) {

		$property = $type . '_dir';
		if ( property_exists( $this, $property ) ) {
			$this->$property = $directory;
		}

	}

	public function getDirectory( $type = 'templates' )
	{

		try {
			if ( ! in_array( $type, [ 'templates', 'compile', 'cache' ] ) ) {
				throw new TemplaterException( sprintf( 'Unknown type, %s', $type ) );
			}
		} catch ( Exception $e ) {
			return $this->handle( $e );
		}

		$dir = $type . '_dir';
		$dir = $this->$dir;
		return $dir;

	}

	public static function hasDot( $name )	
	{

		return false !== strpos( trim( $name, '.' ), '.' );

	}

	public static function getShortName( $name )
	{

		$parts = explode( '.', $name );
		array_shift( $parts );

		$shortName = implode( '.', $parts );
		return $shortName;

	}

	public function invoke()
	{

		if ( ! $this->compiled ) {

			$template = $this->getTemplate();

			$template = $template();
			$compiled = $this->compile( $template );

			$this->compiled = $compiled;
		}

		return $this->compiled;

	}

	public function __invoke( $vars = [], $compile_file = 'compile.php', $print = false )
	{

		return $this->invoke( $vars = [], $compile_file, $print );

	}

	public function handle( $e, int $handle = 1 )
	{

		$this->handle = $handle = isset( $handle ) ? (int) $handle : (int) $this->handle;

		switch ( $handle ) {
			case 1:
				throw $e;
				return;
				break;
			case 0:
				return $e;
				break;
		}

	}

}
