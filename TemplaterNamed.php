<?php

namespace Pf\Templater;

abstract class TemplaterNamed extends TemplaterAbstract {

	protected $name;

	protected function initialize()
	{

		parent::initialize();

	}

	public function getName()
	{

		return $this->name;

	}

}
