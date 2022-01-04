<?php

namespace nathanwooten\Templater;

use Exception;

use nathanwooten\{

	Templater\TemplaterInterface,
	Templater\Templater

};

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php';

class TemplaterVariable implements TemplaterItemInterface {

	protected $name;
	protected $value = null;

	protected $templater = null;

	public function __construct( $name, $value = null, $id = null, Templater $templater = null )
	{

		if ( ! is_null( $templater ) ) {
			$this->templater = $templater;
		}

		$this->setName( $name );

		if ( ! is_null( $value ) ) {
			$this->value = $value;
		}

		if ( ! is_null( $id ) ) {
			$this->id = $id;
		}

	}

	public function __invoke( $id = null )
	{

		$got = $this->get( $id );
		return $got;

	}

	public function get( $id = false, $value = null, $args = null )
	{

		$args = func_get_args();
		if ( isset( $args[0] ) ) {
			$id = $args[0];
		}
		if ( isset( $args[1] ) ) {
			$value = $args[1];
		}
		if ( isset( $args[2] ) ) {
			$input = $args[2];
		}

		$value = is_null( $value ) ? $this->value : $value;
		if ( ! $id ) {
			return $value;
		}
		$ids = explode( '.', $id );

		$type = $this->getType();

		foreach ( $ids as $i ) {
			if ( is_object( $value ) ) {
				if ( $this->isMethod( $value, $i ) ) {

					$method = $i;
					$value =& $value->$method( ...$input );

				} elseif ( $this->isProperty( $value, $i ) ) {

					$property = $i;
					$value =& $value->$property;

				} else {
					throw new Exception( sprintf( 'Property/Method does not exist, %s', $i ) );
				}
			} elseif ( is_array( $value ) ) {
				if ( isset( $value[ $i ] ) ) {
					$value =& $value[ $i ];

				} else {
					throw new Exception( sprintf( 'Index does not exist, %s', $i ) );
				}
			}
		}

		if ( ! is_string( $value ) ) {
			throw new Exception( sprintf( 'Value must evaulate to a string, %s given', gettype( $value ) ) );
		}

		return $value;

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

	public function isMethod( $object, $name ) {

		if ( false !== strpos( $i, '(' ) || false !== strpos( $i, ')' ) ) {
			return false;
		}

		$name = trim( $name, '()' );

		$rms = ( new ReflectionClass( $object ) )->getMethods();
		foreach ( $rms as $rm ) {
			if ( $name === $rm->name ) {
				return true;
			}
		}

		return false;

	}

	public function isProperty( $object, $name ) {

		$rps = ( new ReflectionClass( $object) )->getProperties();
		foreach ( $rps as $rp ) {
			if ( $name === $rp->name ) {
				return true;
			}
		}

		return false;

	}


	public function getType()
	{

		return ucfirst( getType( $this->get() ) );

	}

	public function getParent()
	{

		return $this->parent;

	}

	public function setValue( $value )
	{

		$this->value = $value;

		return $this;

	}

	public function getValue( $id = null )
	{

		return $this->get( $id );

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
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( isset( $this->id ) ) {
			$value = $this->get( $this->id );
			if ( is_string( $value ) ) {
				return $value;
			}
		}

		return '';

	}

}
