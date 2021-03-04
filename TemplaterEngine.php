<?php

namespace Pf\Templater;

use Pf\Templater\TemplaterAbstract;

use Exception;

class TemplaterEngine extends TemplaterAbstract {

	protected static $names = [];

	private function __construct() {}

	public static function set( $namedObject ) {

		$name = $namedObject->getName();
		if ( static::has( $name ) ) {
			throw new Exception( sprintf( 'Name already exists!: %s', $name ) );
		}

		static::$names[$name] = $namedObject;

	}

	public static function get( string $name = '' ) {

		if ( static::has( $name ) ) {
			return static::$names[$name];
		}

		return static::$names;

	}

	public static function has( string $name )
	{

		return array_key_exists( $name, static::$names );

	}

}
