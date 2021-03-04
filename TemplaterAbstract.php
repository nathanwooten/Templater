<?php

namespace Pf\Templater;

abstract class TemplaterAbstract implements TemplaterInterface {

	protected function initialize()
	{

		TemplaterEngine::set( $this );

	}

	public function getNamespace() : string
	{

		return 'Pf\Templater';

	}

	public function getClassname() : string
	{

		return static::class;

	}

	public function getDomain() : string
	{

		return 'Templater';

	}

}
