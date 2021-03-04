<?php

namespace Pf\Templater;

use Pf\Templater\TemplaterNamed;
use Pf\Templater\TemplaterEngine;

class Template extends TemplaterNamed implements TemplateInterface {

	protected $name;

	protected $source;
	protected $template;

	public function __construct( string $name, string $source )
	{

		$this->setName( $name );
		$this->setSource( $source );

		parent::initialize();

	}

	protected function setName( string $name )
	{

		$this->name = $name;

	}

	protected function setSource( $source )
	{

		if ( is_readable( $source ) ) {
			$this->source = $source;
			$this->template = file_get_contents( $this->source );
		} else {
			$this->source = 'input';
			$this->template = $source;
		}

	}

	public function getTemplate()
	{

		return $this->template;

	}

	public function getSource() {

		return $this->source;

	}

	public function render() {

		$result = RenderTemplate::render( $this );
		return $result;

	}

}
