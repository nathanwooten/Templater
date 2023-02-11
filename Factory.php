<?php

namespace nathanwooten\View;

use nathanwooten\View\{

  View,
  Injections

};

class Factory extends Type
{

  public static function create( $view = null, ?Injections $injections = null )
  {

    if ( is_null( $view ) || ! static::isView( $view ) ) {
      $view = View::class;
    }

    if ( ! is_object( $view ) ) {
      $view = static::abstractCreate( $view );
    }

    $view->initInject( $injections );

    return $view;

  }

  public static function abstractCreate( $class, array $args = [] )
  {

    return new $class( ...array_values( $args ) );

  }

  public static function isView( $data )
  {

    return static::isA( $data ) || static::isInstanceOfView( $data );

  }

  public static function isA( $class, $a = View::class )
  {

    if ( ! is_string( $class ) ) {
      return false;
    }

    return is_a( $class, $a );

  }

  public static function isClass( $string )
  {

    if ( ! is_string( $string ) ) {
      return false;
    }

    return class_exists( $string );

  }

  public static function isFile( $string )
  {

    if ( ! is_string( $string ) ) {
      return false;
    }

    return is_readable( $string );

  }

  public static function isInstanceOfView( $data )
  {

    return static::isInstance( $data ) && $data instanceof View;

  }

  public static function isInstance( $data )
  {

    return is_object( $data );

  }

}