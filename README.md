# TonikRouter [![Build Status](https://travis-ci.org/brslv/tonik.svg?branch=master)](https://travis-ci.org/brslv/tonik)

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

## Route tricks

These were the basics. But **tonik** provides some nice route tricks you can make. Let's see.

```php
['GET', '/about/{name?}', 'PagesController@about', 'about'] # {name?} is an optional parameter.
```

```php
['GET', '/about/*', 'PagesController@about', 'about'] # Matches every route, starting with /about.
```

```php
['GET', '/about/( word:(s) )', 'PagesController@about', 'about'] # The 'word' parameter must be a string.
```

The format (something:(constraint)) is a way to restrict route parameters. Say you want the 'something' parameter to be a string - you declare it like this - **(something:(s))** or **( something(s) )**. Other built in constraint is 'n' (for numbers).

If you need something custom, you can pass a regular expression to the brackets. Like this:

```php
['POST', 'foo/{slug:( ([a-z]+)-([a-z]+)-([012]+) )?}', 'PatternsController@complex', 'complex'],
```

This will match only POST requests. The slug parameter should be in the form **"something a-z"-"something a-z again"-"something 0-9"**. You should notice it's also optional. So with or without it this route will be matched. For example **/foo/bar-baz-1** is a valid route. Also just **/foo**. But **/foo/bar** is invalid route, because it doesn't match the regex.

## That's it.
Well, that's pretty much all you would need for a simple app. If you want to dive deeper, take a look at the **tests** folder to see how the router works in greater details.

Happy routing, all.
