# Tonik - a PHP router [![Build Status](https://travis-ci.org/brslv/travis-broken-example.svg?branch=master)](https://travis-ci.org/brslv/travis-broken-example)

Tonik is a simple PHP router with support for optional parameters, patterns and url resolving by route name.

## Basics

Using Tonik is simple. 

* Instantiate the **Tonik\Router** object,
* Call the run method on it.

But Tonik\Router has two dependencies, which you should provide. First - a routes collection object and second - a dispatcher object.

Here's an example:

```php
<?php

$url = $_SERVER['REQUEST_URI']; // The requested url.

$method = $_SERVER['REQUEST_METHOD']; // The HTTP request method (GET, POST, etc...).

$routes = [
    // HTTP method, path, handler, route name.
	['GET', '/', 'HomeController@index', 'homepage'],
    ['GET', '/about', 'PagesController@about', 'about'],
];

$routesCollection = new Tonik\RoutesCollection($routes);
$dispatcher = new Tonik\Dispatcher($url, $method);
$router = new Router($routesCollection, $dispatcher);

$match = $router->run();
```

Now, the matched route and it's specifics should be available to you in the **$match** variable.

## The match

The return value of the **Tonik\Router**'s run() method is a simple array, containing usefull information for the matched route. 

Let's say the requested url **/about/Borislav** matches the route 

```php
['GET', '/about/{name}', 'PagesController@about', 'about']
```

The match will be represented by a simple array like this:

```php
$match = [
	'handler' => 'PagesController@about',
    'params' => [
    	'name' => 'Borislav',
    ],
    'routeName' => 'about',
];
```
