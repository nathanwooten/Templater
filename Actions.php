<?php

namespace nathanwooten\View;

class Actions
{

  public function run( $template, array $variables = [] )
  {

    $source = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'view.tmp.php';
    $put = file_put_contents( $source, $template );

    extract( $variables );
    ob_start();

    include $source;
    $output = ob_get_clean();

    return $output;

  }

  public function label( array $views )
  {

    $fn = function( $item ) {
      return $item->getId();
    };

    return array_combine( array_map( $fn, $views ), $views );

  }

  public function replace( $template, $name )
  {

    $names = is_array( $name ) ? $name : [ $name ];

    foreach ( $names as $item ) {

      if ( ! $item instanceof View ) {
        $id = $this->untag( $item );

      } else {
        $id = $item->getId();
      }

      $template = str_replace( $this->tag( $id ), '<?php print isset( $' . $id . ' ) ? $' . $id . ' : \'\'; ?>', $template );
    }

    return $template;

  }

  public function match( $template, $name = null, $untag = false )
  {

    $matches = [];

    $regex = '/' . $this->tag( is_null( $name ) ? '(.*?)' : $name, 1 ) . '/';
    preg_match_all( $regex, $template, $match );

    if ( $match[0] ) {
      $matches = $match[0];
    }

    if ( $untag ) {
      $matches = array_map( function ( $item ) { return $this->untag( $item ); }, $matches );
    }

    return $matches;

  }

  public function delimiters( $escape = 0 )
  {

    $delimiters = [ '{{', '}}' ];

    if ( $escape ) {
      $delimiters = $this->escape( $delimiters );
    }

    return $delimiters;

  }

  public function escape( array $escape )
  {

    $fn = function ( string $characters ) {
      $characters = str_split( $characters );
      $characters = implode( '\\', $characters );
      return $characters;
    };

    $escape = array_map( $fn, $escape );

    return $escape;

  }

  public function tag( $name, $escape = 0 )
  {

    $name = $this->untag( $name );
    $delimiters = $this->delimiters( $escape );

    $tag = $delimiters[0] . $name . $delimiters[1];
    return $tag;

  }

  public function untag( $tag )
  {

    $trim = $this->delimiters( 0 );
    $trim = array_merge( $trim, $this->delimiters( 1 ) );

    $name = trim( $tag, implode( '', $trim ) );
    return $name;

  }

}