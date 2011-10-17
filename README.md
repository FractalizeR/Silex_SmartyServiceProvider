SmartyServiceProvider
===================

The *SmartyServiceProvider* provides integration with the [Smarty] (http://www.smarty.net) template engine.

Parameters
----------

* **smarty.dir** (optional): Path to the directory containing a file Smarty.class.php

* **smarty.options** (optional): An associative array of smarty
  options. Check out the smarty documentation for more information.

Services
--------

* **smarty**: The ``Smarty`` instance. The main way of interacting with Smarty.

Registering
-----------

Make sure you place a copy of *Smarty* in the ``vendor/Smarty``
directory::
```php
use FractalizeR\Smarty\ServiceProvider as SmartyServiceProvider;
define('SMARTY_PATH', __DIR__ . '/../../../../vendor/Smarty');
        
$app->register(new SmartyServiceProvider(), array(
          'smarty.dir' => SMARTY_PATH,
          'smarty.options' => array(
                'template_dir' => SMARTY_PATH . '/demo/templates',
                'compile_dir' => SMARTY_PATH . '/demo/templates_c',
                'config_dir' => SMARTY_PATH . '/demo/configs',
                'cache_dir' => SMARTY_PATH . '/demo/cache',),));
```

**Note:**

Smarty is not compiled into the ``silex.phar`` file. You have to  add your own copy of Smarty to your application.

Usage
-----

The Smarty provider provides a ``smarty`` service::
```php
$app->get('/hello/{name}', function ($name) use ($app) {
    return $app['smarty']->display('hello.tpl', array(
        'name' => $name,
    ));
});
```

This will render a file named ``hello.tpl`` in the configured templates folder you passed in ``smarty.options``.

In any Smarty template, the ``app`` variable refers to the Application object.
So you can access any services from within your view. For example to access
``$app['request']->getHost()``, just put this in your template:

```
{$app.request.host}
```
