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

	}

	public function getName()
	{

		return $this->name;

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

	public function hasTemplater()
	{

		return isset( $this->templater );

	}

	public function getTemplater()
	{

		if ( $this->hasTemplater() ) {
			return $this->templater;
		}

	}

}