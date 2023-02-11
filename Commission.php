<?php

namespace nathanwooten\View;

use nathanwooten\View\{

  ViewInterface,
  Factory

};

trait Commission
{

  public function setParent( ViewInterface $view )
  {

    $this->parent = $view;

    return $this;

  }

  public function getParent()
  {

    return $this->parent;

  }

  public function hasParent()
  {

    return isset( $this->parent );

  }

  public function setView( $view, $id = null )
  {

    if ( ! is_object( $view ) ) {
      if ( class_exists( $view ) ) {
        $class = $view;
        $template = null;
      } else {
        $class = View::class;
        $template = $view;
      }

      $view = Factory::create( $class, new Injections( null, [ 'initParts' => [ null, $template ] ] ) );
    }

    if ( ! is_null( $id ) ) {
      $view->setId( $id );
    }

    $this->views[] = $view;
	$view->setParent( $this );

    return $this;

  }

  public function getView( $id = null )
  {

    $container = $this->getViews();

    while( ! empty( $container ) ) {
      $view = array_shift( $container );
var_dump( $view->getId () );
      if ( $id === $view->getId() ) {
        break;
      } else {
        $view = null;
      }
    }

    if ( ! isset( $view ) ) {
      return;
    }

    return $view;

  }

  public function hasView( $id )
  {

    return $this->getView( $id );

  }

  public function getViews()
  {

    return isset( $this->views ) ? $this->views : [];

  }

  public function setViews( ?array $views = null )
  {

    $this->views = $views;

    return $this;

  }

  public function unsetViews()
  {

    unset( $this->views );

  }

  public function contain( array $container )
  {

    foreach ( $container as $key => $item ) {
      $this->setView( $item, is_string( $key ) ? $key : null );
    }

    return $this;

  }

}