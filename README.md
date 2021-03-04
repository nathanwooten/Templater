# Templater
A simple template engine.

```php
use Pf\Templater\Template;
use Pf\Templater\TemplaterEngine;

require_once HBDIR . 'Templater' . DS . 'src' . DS . 'index.php';

$template = [

	'document' => USERDIR . 'static' . DS . 'template' . DS . 'document.php',
	'header' => USERDIR . 'static' . DS . 'template' . DS . 'header.php',
	'body' => USERDIR . 'static' . DS . 'template' . DS . 'body.php',
	'footer' => USERDIR . 'static' . DS . 'template' . DS . 'footer.php',

	'name' => 'Nathan {{last}}',
	'last' => '"Nate" Wooten'

];

foreach ( $template as $name => $source ) {
	new Template( $name, $source );
}

$doc = TemplaterEngine::get( 'document' );
$document = $doc->render();
print $document;
```
