<?php

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';

$view = new nathanwooten\View\View;
$view->set( '<h1>Hello World</h1><p>This is a hello world document.</p>' );
$template = $view->getResponse();
print $template;