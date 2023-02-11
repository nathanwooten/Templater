<?php

if ( ! defined( 'PUBLIC_HTML' ) ) {
  require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';
}
if ( ! defined( 'PUBLIC_HTML' ) ) {
  die( 'Error, please contact the administractor' );
}

extract( ENTRY );

require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';

use nathanwooten\{

  Registry\Registry,
  Container\Containers\ApplicationContainer as Container,

  Collection\Collection

};

require_once PUBLIC_HTML . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'page.php';