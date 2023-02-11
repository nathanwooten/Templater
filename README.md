# Templater
A simple template engine.

```
<?php

// the require file autoloads the package
// in the View/src/index.php
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';
```

## Example 1

Simple example to output any string template.

```
<?php
// Example 1

$view = new nathanwooten\View\View;
$view->set( '<h1>Hello World</h1><p>This is a hello world document.</p>' );
print $view;
```

### Outputs

#Hello World

This is a hello world document.

## Example 2

```
<?php
// Example 2

class HelloWorldDate extends nathanwooten\View\View
{

  protected string $response = '2/11/2023';

}

class HelloWorld extends nathanwooten\View\View
{

  protected ?string $template = '<h1>Hello World</h1><p>This is a hello world document. The date is: {{date}}</p>';

  protected array $views = [
    'date' => HelloWorldDate::class
  ];

  public function __construct()
  {

    $this->initContain();

  }

}

$view = new HelloWorld;
print $view;
```

### Outputs

#Hello World

This is a hello world document. The date is 2/11/2023
