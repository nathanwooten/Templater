<?php

$dir = dirname( __FILE__ );
$file = basename( __FILE__ );

foreach ( scandir( $dir ) as $item ) {
  if ( '.' === $item || '..' === $item || $file === $item ) {
    continue; 
  }
  require_once $dir . DIRECTORY_SEPARTOR . $item;
}
