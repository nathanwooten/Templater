<?php

$tplRegister = function ( $interface )
{

  $file = str_replace( 'nathanwooten\View', dirname( __FILE__ ), $interface . '.php' );

  require_once $file;

};

spl_autoload_register( $tplRegister, true, true );