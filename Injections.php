<?php

namespace nathanwooten\View;

use nathanwooten\View\{

  Type

};

class Injections extends Type
{

  public $properties = [];
  public $methods = [];

  public function __construct( ?array $properties = null, ?array $methods = null )
  {

    if ( ! is_null( $properties ) ) {
      $this->setProperties( $properties );
    }

    if ( ! is_null( $methods ) ) {
      $this->methods = $methods;
    }

  }

  public function inject( $obj )
  {

    if ( ! is_object( $obj ) ) {
      throw new Exception( 'Injecting object only, provided: ' . gettype( $obj ) );
    }

    foreach ( $this->getProperties() as $propertyName => $value ) {
      $obj->$propertyName = $value;
    }

    foreach ( $this->getMethods() as $methodName => $args ) {
      $obj->$methodName( ...array_values( $args ) );
    }

  }

  public function setProperties( array $properties )
  {

    $this->properties = $properties;

  }

  public function getProperties()
  {

    return $this->properties;

  }

  public function setMethods( array $methods )
  {

    $this->methods = $methods;

  }

  public function getMethods()
  {

    return $this->methods;

  }

}