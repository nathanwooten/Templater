# Templater
A simple template engine.

```php
use nathanwooten\Templater\Templater;

$templater = new Templater( 'path/to/templates', [ '{{', '}}' ] );

$templates = [
    'page' => 'page.php',
    'header' => 'header.php',
    'body' => 'body.php',
    'footer' => 'footer.php'
];

/**
 * Sets each name/template pair
 * with setTemplate method, creating
 * new Template objects
 */

$templater->setTemplates( $templates );

$templater->setVariable( 'title', 'example.com' );

/**
 * The base template will always resolve to the first template set
 */

$baseTemplate = $templater();
print $baseTemplate;

```
