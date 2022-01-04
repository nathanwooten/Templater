<?php

namespace nathanwooten\Templater;

use nathanwooten\{

	Templater\Templater

};

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php';

class TemplaterTemplate implements TemplaterItemInterface {

	protected $templater;

	protected $name;
	protected $source;
	protected $template;

	protected $containsPhp = false;

	public function __construct( $name, $template, Templater $templater = null )
	{

		$this->templater = $templater;

		$this->setName( $name );
		$this->setTemplate( $template );

	}

	public function __invoke()
	{

		return $this->get();

	}

	public function get()
	{

		return $this->getTemplate();

	}

	public function setName( $name )
	{

		$this->name = $name;
		if ( $this->hasTemplater() ) {
			$templater = $this->getTemplater();

			if ( ! $templater->has( $name ) ) {
				$templater->setVariable( $name, $this );

			}
		}

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

	public function setTemplate( $template )
	{

		$this->source = $template;

		if ( is_file( $template ) && is_readable( $template ) ) {

			$this->template = file_get_contents( $template );
			return;
		}

		if ( $this->hasTemplater() ) {

			$with = $this->getDirectory() . $template;
			if ( is_file( $with ) && is_readable( $with ) ) {

				$this->template = file_get_contents( $with );
				return;
			}
		}

		$this->template = $template;
		return;

	}

	public function getTemplate()
	{

		return $this->template;

	}

	public function getDirectory()
	{

		$dir = $this->getTemplater()->getDirectory();
		return $dir;

	}

	public function containsPhp( $default = null )
	{

		$args = func_get_args();
		if ( ! isset( $args[1] ) && $this->hasTemplater() ) {

			$templater = $this->getTemplater();
			return $templater->containsPhp( $default, $this );
		}

		if ( is_null( $default ) ) {
			return $this->containsPhp;
		}

		return $this->containsPhp = $default;

	}

	public function setTemplater( $templater = null )
	{

		$this->templater = $templater;

	}

	public function getTemplater()
	{

		return isset( $this->templater ) ? $this->templater : null;

	}

	public function hasTemplater()
	{

		return isset( $this->templater );

	}

	public function __toString()
	{

		$value = $this->get();
		if ( ! is_string( $value ) ) {
			return '';
		}

		return $value;

	}

}
