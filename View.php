<?php

namespace nathanwooten\View;

use Exception;

use nathanwooten\View\{

  ViewInterface,
  Type

};

/**
 * View class defines the elements of a view including
 * template, id, parent, children, callback, response, etc
 */

if ( ! class_exists( 'nathanwooten\View\View' ) ) {
class View extends Type implements ViewInterface
{

  /**
   * The callback to generate the view response
   * if not set, the Compiler::compile method is called
   */

  protected $callback;

  /**
   * The ouput of the view after compilation
   * it is a string
   * to be passed the Response object
   */

  protected string $response;

  /**
   * This is the view's parent
   */

  protected ?ViewInterface $parent = null;

  /**
   * The ID and actual template
   * of the view
   */

  protected ?string $id = null;
  protected ?string $template = null;

  /**
   * An array of children views
   */

  protected array $views = [];

  /**
   * The compiler object which
   * hooks into the actions
   */

  protected $compiler = Compiler::class;

  /**
   * Set id and template initialization
   */

  public function initParts( $id = null, ?string $template = null )
  {

    if ( ! is_null( $id ) ) {
      $this->setId( $id );
    }

    if ( ! is_null( $template ) ) {
      $this->set( $template );
    }

  }

  /**
   * Initialize preset views items,
   * by creating/resetting view objects
   */

  public function initContain( ?array $container = null )
  {

    if ( is_null( $container ) ) {
      $container = $this->getViews();
      $this->unsetViews();
    }

    $this->contain( $container );

  }

  /**
   * Set this view's template
   */

  public function set( string $template ) : ViewInterface
  {

    $this->template = $template;

    return $this;

  }

  /**
   * Fetch this view's template
   */

  public function get() : string
  {

    return (string) $this->template;

  }

  /**
   * Returns whether or not
   * a template has been set
   */

  public function has()
  {

    return $this->template;

  }

  /**
   * Get the manually set id,
   * or use the internal id, which is
   * basename( FQCN )
   */

  public function getId()
  {

    return ! is_null( $this->id ) ? $this->id : basename( static::class );

  }

  /**
   * Set the ID or VAR name
   */

  public function setId( $id )
  {

    $this->id = $id;

  }

  /**
   * Does this view already have an id,
   * or will getId pull up the internal id
   */

  public function hasId()
  {

    return is_null( $this->id );

  }

  /**
   * Set callback to be run that outputs
   * the response of the template, and
   * if the base template, to be passed
   * to a Response object
   */

  public function setCallback( callable $callback = null )
  {

    if ( is_null( $callback ) ) {
      $callback = [ $this->getCompiler( $this->compiler ), 'compile' ];
    }

    $this->callback = $callback;

  }

  public function getCallback()
  {

    if ( ! isset( $this->callback ) ) {
      return null;
    }

    return $this->callback;

  }

  public function setResponse( string $response )
  {

    $this->response = $response;

  }

  /**
   * If response is set, return it,
   * if not set, and callback is set,
   * call callback and set it to response
   */

  public function getResponse()
  {

    if ( ! isset( $this->response ) ) {
      if ( ! isset( $this->callback ) ) {
         $this->setCallback();
      }
      $callback = $this->callback;

      $this->response = $callback();
    }

    return $this->response;

  }

  /**
   * Check that a value
   * has been set to response property
   */

  public function hasResponse()
  {

    return ! is_null( $this->response );

  }

  /**
   * Set a child view to be parsed
   * when the response callback is called
   */

  public function setView( ViewInterface $view )
  {

    $this->views[] = $view;

  }

  /**
   * Fetch a child view by ID
   */

  public function getView( $id )
  {

    foreach ( $this->views as $view ) {
      if ( $id === $view->getId() ) {
        return $view;
      }
    }

  }

  /**
   * Find whether or not a child view exists
   */

  public function hasView( $id )
  {

    foreach ( $this->views as $view ) {
      if ( $id === $view->getId() ) {
        return true;
      }
    }

    return false;

  }

  /**
   * Set an array of views
   * does not reset
   */

  public function setViews( array $views )
  {

    foreach ( $views as $view ) {
      $this->setView( $view );
    }

  }

  /**
   * Return the views array
   */

  public function getViews()
  {

    return $this->views;

  }

  /**
   * Check that the views property isn't empty
   */

  public function hasViews()
  {

    return ! empty( $this->views );

  }

  /**
   * Unset a specific view
   */

  public function unsetView( $id )
  {

    foreach ( $this->views as $index => $view ) {
      if ( $id === $view->getId() ) {
        unset( $this->views[ $index ] );

        break;
      }
    }

  }

  /**
   * Unset all views
   */

  public function unsetViews()
  {

    $this->views = [];

  }

  /**
   * Get this view's instance of the compiler
   */

  public function getCompiler( $class = null )
  {

    if ( ! isset( $this->compiler ) || is_string( $this->compiler ) ) {

      if ( ! is_null( $class ) && is_a( $class, Compiler::class ) ) {
      } else {
        $class = Compiler::class;
      }

      $this->compiler = new $class( $this );
    }

    return $this->compiler;

  }

  /**
   * All views must be stringable
   */

  public function __toString() : string
  {

    $response = (string) $this->getResponse();
    return $response;

  }


}
}