<?php

namespace nathanwooten\View;

use Exception;

use nathanwooten\View\{

  ViewInterface,
  Type,
  Commission

};

class View extends Type implements ViewInterface
{

  use Commission;

  protected $response;

  protected ?ViewInterface $parent = null;

  protected ?string $id = null;
  protected ?string $template = null;

  protected array $views = [];

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

  public function setResponse( callable $callback = null )
  {

    if ( is_null( $callback ) ) {
      $callback = [ $this->getCompiler( $this->compiler ), 'compile' ];
    }

    $this->response = $callback;

  }

  public function getResponse()
  {

    if ( ! isset( $this->response ) ) {
      $this->setResponse();
    }

    if ( is_callable( $this->response ) ) {
      $callback = $this->response;

      $this->response = $callback();
    }

    return $this->response;

  }

  public function hasResponse()
  {

    return ! is_null( $this->response );

  }

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

  public function __toString()
  {

    $response = (string) $this->getResponse();
    return $response;

  }


}

/*
class View implements ViewInterface
{

  public string $response = '';

  protected $id;
  protected $view;

  protected $commissioner = null;
  protected $parent = null;
  protected ?array $container = null;

  public function __construct( $id = null, string $view = null, ?array $container = null, ViewInterface $commissioner = null )
  {

    $this->id = $id ?? ( isset( $this->id ) ? $this->id : null );
    $this->commissioner = $commissioner ?? $this->commissioner;

    $this->view = $view ?? $this->view;

    if ( $this->isView( $this->get() ) ) {
      $this->delegate( $this );

    } else {
      if ( ! is_null( $this->container ) ) {
        $this->contain();
      }

    }

  }

  public function getId()
  {

    return $this->id ?? basename( get_class( $this ) );

  }

  public function setId( $id )
  {

    $this->id = $id;

  }

  public function setResponse( ?string $response = null )
  {

    $this->response = $response;

  }

  public function getResponse()
  {

    $view = $this;
    while ( $view->hasDelegate() ) {
      $view = $view->getDelegate();
    }

    $response = is_null( $view->response ) || empty( $view->response ) ? '' : $view->response;

    return $response;

  }

  public function run()
  {

    $response = $this->getResponse();
    if ( is_null( $response ) || empty( $response ) ) {
      $this->compiler()->compile();
    }

    return $this->getResponse();

  }

  public function compiler()
  {

    $compiler = new ViewCompiler( $this );
    return $compiler;

  }

  public function set( $view )
  {

    $this->view = $view;

  }

  public function get()
  {

    return $this->view;

  }

  public function setView( $view, $id = null )
  {

    if ( ! is_object( $view ) ) {
      $view = $this->create( $view );
    }

    $this->container[] = $view;
    if ( $id ) {
      $view->setId( $id );
    }

    $view->setParent( $this );

  }

  public function getView( $id = null )
  {

    $container = $this->container;

    while( $container ) {
      $view = array_shift( $container );

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

    return isset( $this->container ) ? $this->container : [];

  }

  public function setViews( ?array $container = null )
  {

    $this->container = $container;

  }

  public function unsetViews()
  {

    unset( $this->container );

  }

  public function contain( ?array $container = null )
  {

    if ( is_null( $container ) ) {
      if ( is_null( $this->container ) ) {
        return;
      }
      $container = $this->container;

    } elseif ( ! is_null( $this->container ) ) {
      foreach ( $this->container as $item ) {
        $container[] = $item;
      }

    } else {
      return;
    }

    $this->setViews( [] );

    foreach ( $container as $key => $item ) {

      if ( ! is_object( $item ) ) {

        $item = (array) $item;
        $item = $this->create( ...$item );

        $item->setParent( $this );

        $id = null;
        if ( is_string( $key ) ) {
          $id = $key;
        }

        $this->setView( $item, $id );
      }
    }

    return $this;

  }

  public function setParent( View $view )
  {

    $this->parent = $view;

  }

  public function getParent()
  {

    return $this->parent;

  }

  public function setCommissioner( ViewInterface $view )
  {

    $this->commissioner = $view;

  }

  public function getCommissioner()
  {

    return $this->commissioner ?? null;

  }

  public function hasCommissioner()
  {

    return isset( $this->commissioner );

  }

  public function getDelegate()
  {

    return $this->get();

  }

  public function hasDelegate()
  {

    return $this->isView( $this->get() );

  }

  public function delegate( $view )
  {

    if ( ! $view instanceof self ) {
      throw new Exception( 'Can not delegate non-view, ' . gettype( $view ) );
    }

    $commissioner = $delegate = $view;

    $container = $view->getViews();
    $view->setViews( [] );

    while ( $this->isView( $commissioner->get() ) ) {

      $got = $commissioner->get();

      if ( ! is_object( $got ) ) {
        $delegate = $this->create( $got );
      } else {
        $delegate = $got;
      }

      $commissioner->set( $delegate );
	  $delegate->setCommissioner( $commissioner );

      $views = (array) $commissioner->getViews();
      foreach ( $views as $view ) {
        $container[] = $view;
      }

      $commissioner = $delegate;
    }

    $delegate->setViews( $container );
    $delegate->contain();

    return $delegate;

  }

  public static function isView( $view )
  {

    return ( is_string( $view ) && class_exists( $view ) ) || ( is_object( $view ) && $view instanceof View );

  }

  public static function create( $view, ?array $args = null )
  {

    if ( ! static::isView( $view ) ) {
      $view = new self( null, $view );
    } else {
      $view = new $view( ...array_values( (array) $args ) );
    }

    if ( ! $view instanceof View ) {
      throw new Exception( 'A view must be a View object, processed type: ' . gettype( $view ) );
    }

    return $view;

  }

  public function __toString()
  {

    $string = $this->run();
    return $string;

  }

}*/