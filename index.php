<?php

if ( ! defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

$dir = dirname( __FILE__ ) . DS;

require_once dirname( $dir ) . DS . 'vendor' . DS . 'Profordable' . DS . 'ProfordableInterface.php';

require_once $dir . 'TemplaterInterface.php';
require_once $dir . 'TemplaterAbstract.php';
require_once $dir . 'TemplaterEngine.php';
require_once $dir . 'TemplaterNamed.php';
require_once $dir . 'TemplateInterface.php';
require_once $dir . 'Template.php';
require_once $dir . 'RenderTemplate.php';
