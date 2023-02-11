# Templater
A simple template engine.

```
<?php

// the require file autoloads the package
// in the View/src/index.php
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';

// Example 1

$view = new nathanwooten\View\View;
$view->set( '<h1>Hello World</h1><p>This is a hello world document.</p>' );
$template = $view->getResponse();
print $template;

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

##Outputs

<?php

// the require file autoloads the package
// in the View/src/index.php
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'require.php';

// Example 1

$view = new nathanwooten\View\View;
$view->set( '<h1>Hello World</h1><p>This is a hello world document.</p>' );
$template = $view->getResponse();
print $template;

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

?>
