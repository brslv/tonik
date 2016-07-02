<?php

use Borislav\Broute\Entities\Route;
use Borislav\Broute\RoutesCollection;

class RoutesCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testItRegistersRouteCorrectly()
    {
        $routes = [
            ['GET', '/', 'HomeController@index', 'home'],
            ['GET', '/about', 'PagesController@about', 'about'],
            ['POST', '/articles', 'ArticlesController@create', 'createArticle'],
        ];

        $routesCollection = new RoutesCollection($routes);

        $expect = [
            'GET' => [
                new Route($routes[0]),
                new Route($routes[1]),
            ],
            'POST' => [
                new Route($routes[2]),
            ],
            'PUT' => [],
            'DELETE' => [],
            'OPTIONS' => [],
        ];

        $this->assertEquals($expect, $routesCollection->routes());
    }
}
