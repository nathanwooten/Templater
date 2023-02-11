<?php

namespace nathanwooten\View;

use Exception;

use nathanwooten\View\{

  Type,
  Actions

};

class Compiler extends Type
{

  protected $compiled = false;

  protected $template = [];
  protected $matches = [];

  public function __construct( View $view )
  {

    $this->view = $view;
    $this->setTemplate( $view->get() );

  }

  public function compile()
  {

    if ( ! $this->compiled ) {

      $this->replace();
      $run = $this->run();

      $this->setTemplate( $run );
    }

    return $this->compiled = $this->getTemplate();

  }

  public function replace()
  {

    $template = $this->getActions()->replace( $this->getTemplate(), $this->match() );
    $this->setTemplate( $template );

    return $template;

  }

  public function run()
  {

    return $this->getActions()->run( $this->getTemplate(), $this->label() );

  }

  public function label()
  {

    return $this->getActions()->label( $this->getViews() );
//    return $this->getActions()->label( $this->getViews( $this->match() ) );

  }

  public function match()
  {

    $tCount = count( $this->template );
    $key = $tCount > 0 ? $tCount -1 : $tCount;

    if ( ! isset( $this->matches[ $key ] ) ) {
      $this->matches[ $key ] = $this->getActions()->match( $this->getTemplate(), null, true );
    }

    return $this->matches[ $key ];

  }

  protected function setTemplate( ?string $template = null )
  {

    $this->template[] = $template;
    $this->match();

    return $this;

  }

  public function getTemplate( $key = null )
  {

    if ( is_null( $key ) ) {
      $templates = $this->endTemplate()->template;

      $template = current( $templates );
      return $template;
    }

    $template = isset( $this->template[ $key ] ) ? $this->template[ $key ] : null;
    return $template;

  }

  /**
   * Prep the template property
   * for retrieval
   * of the last value in the array
   */

  public function endTemplate()
  {

    end( $this->template );
    return $this;

  }

  /**
   * Get the response of the compiler
   */

  public function getResponse()
  {

    return $this->compile();

  }

  /**
   * Fetch the view set in the constructor,
   * for the compiler instance.
   */

  public function get()
  {

    return $this->view;

  }

  /**
   * Get the vars of the template
   */

  public function getViews( $names = null )
  {

    return $this->get()->getViews( $names );

  }

  /**
   * Get the compiler Actions object
   */

  public function getActions()
  {

    if ( ! isset( $this->actions ) ) {
      $this->actions = new Actions;
    }

    return $this->actions;

  }

}