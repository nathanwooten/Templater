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

Using arrays for variables, as you may when extracting data from a database:

```php
use nathanwooten\Templater\Templater;

$templater = new Templater( 'path/to/templates', [ '{{', '}}' ] );
$templater->setVariable( 'content', [ 'title' => 'This is a Title', 'content' => 'This is the Content' ] );

```

Now in the template, with the above settings, you would use the following to access your variables:

```
<h1>{{content.title}}</h1>

<div>{{content.content}}</div>
```

If you were using objects created from your databaser, you could do this:

```
<h1>{{content.getTitle()}}</h1>

<div>{{content.content}}</div>
```

Notice either syntax above works, the first content.getTitle() calls the getTitle method of the content object, where content.content gets the content property of the content object.

Now, let's say you want to replace variables before templates in all your templates instead of the other way around, you might go about something like that in this fashion:

```php
use nathanwooten\Templater\Templater;

$templater = new Templater( 'path/to/templates', [ '{{', '}}' ] );

$baseTemplate = $templater->getTemplate();

$baseTemplate = $templater->compileTemplate( $baseTemplate, $templater->getVariables() );
$baseTemplate = $templater->compileTemplate( $baseTemplate, $templater->getTemplates() );

print $baseTemplate;

```
