<?php

include 'vendor/autoload.php';

use Borislav\Broute\Router;
use Borislav\Broute\Dispatcher;
use Borislav\Broute\RoutesCollection;

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$routes = [
    ['GET', '/', 'home', 'home'],
    ['GET', '/{ertin:(s)?}', 'Star', 'star'],
];

$routesCollection = new RoutesCollection($routes);
$dispatcher = new Dispatcher($url, $method);
$router = new Router($routesCollection, $dispatcher);

$match = $router->run();

var_dump($match);
