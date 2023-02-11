<?php

namespace nathanwooten\View;

use nathanwooten\Registry\{

  Registry

};

use nathanwooten\Container\Containers\{

  ApplicationContainer as Container

};

class Type
{

  public function init( string $methodName, array $args = [] )
  {

    $method = [ $this, 'init' . $methodName ];
    if ( ! is_callable( $method ) ) {
      throw new Exception( 'Unable to use provided initialize method, please check your values' );
    }

    return $method( ...array_values( $args ) );

  }

  public function initInject( Injections $injections = null )
  {

    if ( ! is_null( $injections ) ) {
      $injections->inject( $this );
    }

  }

  public function getContainer()
  {

    return Registry::get( Container::class );

  }

}