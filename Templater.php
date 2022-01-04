<?php

namespace nathanwooten\Templater;

use Exception;

use nathanwooten\{

	Templater\TemplaterItemInterface,
	Templater\TemplaterException,
	Templater\TemplaterTemplate as Template,
	Templater\TemplaterVariable as Variable

};

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php';

class Templater implements TemplaterItemInterface {

	/**
	 * The optional name of the templater
	 */

	public $name = null;

	/**
	 * This is the compiled base template
	 * of the Templater, all other
	 * templates are part of this template,
	 * and it's output gets stored here
	 */

	protected $compiled = null;

	/**
	 * This is an associative array
	 * of template objects
	 */

	protected $templates = [];

	/**
	 * This is an associate array
	 * of variable objects
	 */

	protected $variables = [];

	/**
	 * The directory in which
	 * the template file(s) reside, optional,
	 * as long as you provide absolute files,
	 * in the event this is not provided.
	 */

	protected $directory = '';

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

	/**
	 * Flag to 'universally' determine
	 * whether or not PHP is being used,
	 * in templates, defaults to null,
	 * in which case, PHP usage is determined
	 * on a template by template basis.
	 */

	protected $containsPhp = null;

	public $handle = 1;

	/**
	 * All parameters optional but recommended,
	 * the name, templates_dir and delimiters
	 * can be provided, if delimiters aren't provided
	 * the templater will not know
	 * how to parse your tags and you will have to
	 * rely on PHP in your templates for variables.
	 */

	public function __construct( $name = null, $directory = null, array $delimiters = null )
	{

		if ( ! is_null( $name ) ) {
			$this->name = $name;
		}

		if ( ! is_null( $directory ) ) {
			$this->directory = $directory;
		} else {
			$this->directory = '';
		}

		if ( ! is_null( $delimiters ) ) {
			$this->delimiters = $delimiters;
		}

	}

	/**
	 * This is a TemplaterItemInterface object,
	 * therefore, it can be added as a template,
	 * to another templater.
	 */

	public function get()
	{

		$templater = $this;
		$compiled = $templater();

		return $compiled;

	}

	public function has( $name ) {

		if ( array_key_exists( $name, $this->templates ) ) {
			return true;
		}

		return false;

	}


	public function setName( $name )
	{

		$this->name = $name;

		return $this;

	}

	public function getName()
	{

		return isset( $this->name ) ? $this->name : null;

	}

	public function hasName()
	{

		return isset( $this->name );

	}

	/**
	 * Set a template, in the form of,
	 * a template object, a template string, or
	 * a filename/directory-filename.
	 * If the directory of the templater is not set,
	 * at compile time, the input here intended to be files,
	 * will have to be absolute-readable directory-filenames
	 */

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

	/**
	 * Retrieve a template by name, defaults to 'base' template
	 * for retrieval by templater invocation. You can however,
	 * fetch any template you want as long as the provided name,
	 * exists in the keys of the templates property.
	 */

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

	/**
	 * Set an array of templates,
	 * return this.
	 */

	public function setTemplates( array $templates )
	{

		foreach ( $templates as $name => $template ) {
			$this->setTemplate( $name, $template );
		}

		return $this;

	}

	/**
	 * Fetch all templates as an associative array, of either
	 * template objects or a resolved strings.
	 */

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

	/**
	 * Set a variable with object, or value input
	 * Variables do not have to resolve to strings,
	 * however they are not parsed like templates
	 * are and are expected to provide data as-is.
	 */

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

	/**
	 * Get a variable from the variables array,
	 * by name returning null if name does not
	 * exist in the array
	 */

	public function getVariable( $name )
	{

		return array_key_exists( $name, $this->variables ) ? $this->variables[ $name ] : null;

	}

	/**
	 * Set an array of variables,
	 * providing an associative array
	 * of valid variable input
	 */

	public function setVariables( array $vars = [] )
	{

		foreach ( $vars as $name => $value ) {
			$this->setVariable( $name, $value );
		}

		return $this;

	}

	/**
	 * Get all of the templater variables
	 */

	public function getVariables()
	{

		return $this->variables;

	}

	/**
	 * Compile a template, including replacing
	 * tags with templates and variables and
	 * running the PHP in the template and capturing
	 * the ouput and saving it to a file.
	 */

	public function compile( $item, $id = null, $containsPhp = null )
	{

		$input = array_merge( $this->getVariables(), $this->getTemplates() );

		$template = $this->compileTemplate( $item, $input );

		if ( $this->containsPhp( $item, $containsPhp ) ) {

			$template = $this->compilePhp( $template, $input );
		}

		return $template;

	}

	/**
	 * Perform all non-PHP parsing
	 * compile operations including,
	 * replacing tags and also, very importantly,
	 * parsing interior templates
	 */

	public function compilePhp( $item, array $input = [] )
	{

		$name = 'compilation';

		if ( $item instanceof TemplaterItemInterface ) {
			$name = $item->hasName() ? $item->getName() : $name;
			$template = $item->get();
		} else {
			$template = $item;
		}

		$file = $this->getDirectory() . 'toCompile_' . $name . '.php';
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

		$filename = 'compiled_' . $name . '.php';
		$file = $this->getDirectory() . $filename;

		$put = file_put_contents( $file, $rendered );

		return $rendered;

	}

	public function containsPhp( $default = null, $item = null )
	{

		if ( ! is_null( $default ) ) {

			if ( is_null( $item ) || ! $item instanceof TemplaterItemInterface ) {
				return $this->containsPhp = $default;

			} else {
				$containsPhp = $item->containsPhp( $default, true );				
				return $containsPhp;

			}

		} else {

			if ( isset( $this->containsPhp ) ) {
				return $this->containsPhp;

			} else {
				return $default;

			}
		}

	}

	public function compileTemplate( $template, array $input = [], $strip = true )
	{

		if ( $template instanceof Template ) {
			$templateString = $template->getTemplate();
		} else {
			$templateString = $template;
		}

		$matches = $this->match( $templateString );
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
					$match->setTemplate( $this->compile( $match, $input ) );
				}

				$value = $match->get( $shortName );

			} elseif ( $strip ) {

				$value = '';
			}

			$templateString = $this->compileReplace( $name, $value, $templateString );

		}

		if ( $template instanceof Template ) {
			$template->setTemplate( $templateString );
		} else {
			$template = $templateString;
		}

		return $template;

	}

	/**
	 * Perform a name, value, template replace operation
	 * Only takes string input.
	 */

	public function compileReplace( $name, $value, $template )
	{

		if ( ! is_string( $name ) || ! is_string( $value ) || ! is_string( $template ) ) {
			throw new Exception( 'String inputs only on this method, ' . __METHOD__ );
		}

		$replace = $this->delimit( $this->remove( $name ), [], 1 );

		$template = str_replace( $replace, $value, $template );
		return $template;

	}

	/**
	 * Match a tag or all tags,
	 * in the given template,
	 * using the delimiters that
	 * have already been provided
	 * to the templater.
	 */

	public function match( $template, $specific = null )
	{

		$expression = is_null( $specific ) ? '(.*?)' : $specific . '(.*?)';

		$regex = '/' . $this->delimit( $expression ) . '/';
		preg_match_all( $regex, $template, $match );

		$match = $match[0];

		return $match;

	}

	/**
	 * Delimit a name with option for use with,
	 * regex or names.
	 */

	public function delimit( $name, $delimiters = [], $tag = 0 )
	{

		$delimiters = $this->delimiters( $delimiters, $tag );

		$delimited = $delimiters[0] . trim( $name, $delimiters[0] . $delimiters[1] ) . $delimiters[1];

		return $delimited;

	}

	/**
	 * Remove delimiters from tag
	 * making it a name.
	 */

	public function remove( $delimited )
	{

		$name = str_replace( $this->delimiters( $this->delimiters, 1 ), '', $delimited );
		return $name;

	}

	/**
	 * Returns delimiters, escaped by default,
	 * for use in regular expressions, or raw,
	 * for use with names depending on the
	 * tag parameter.
	 */

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

	public function setDirectory( $directory ) {

		$this->directory = $directory;

	}

	public function getDirectory()
	{

		return $this->directory;

	}

	/**
	 * Determines if a name contains a dot.
	 */

	public static function hasDot( $name )	
	{

		return false !== strpos( trim( $name, '.' ), '.' );

	}

	/**
	 * Shifts the first element off of the array
	 * of a name containing a dot,
	 * implodes and returns the result.
	 */

	public static function getShortName( $name )
	{

		$parts = explode( '.', $name );
		array_shift( $parts );

		$shortName = implode( '.', $parts );
		return $shortName;

	}

	/**
	 * The target of the invocation method and,
	 * the method that will automatically,
	 * parse the base template and all children
	 * templates, save the compilation and
	 * return it.
	 */

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

	/**
	 * The invocation method that makes the templater
	 * object callable. Calls the "invoke" method.
	 */

	public function __invoke()
	{

		return $this->invoke();

	}

	/**
	 * Exception are fed into this method,
	 * to ease detection of throw vs pass.
	 */

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
