<?php

use Tonik\Entities\Route;

class RouteTest extends PHPUnit_Framework_TestCase 
{
    public function testItCanInstantiate()
    {
        $route = ['GET', '/', 'HomeController@index', null];

        $route = new Route($route);

        $this->assertEquals(true, is_object($route));
    }

    public function testItRecognizesRouteInformtion()
    {
        $route = [
            'POST',
            '/some/foo/bar',
            'FooBarController@baz',
            'fooBarBaz',
        ];

        $route = new Route($route);

        $this->assertEquals(['POST'], $route->methods());
        $this->assertEquals('/some/foo/bar', $route->url());
        $this->assertEquals('FooBarController@baz', $route->handler());
        $this->assertEquals('fooBarBaz', $route->name());
    }

    public function testItThrowsExceptionForInvalidMethod()
    {
        $this->setExpectedException('InvalidArgumentException');

        $route = [
            'CHEEZE',
            '/some/foo/bar',
            'FooBarController@baz',
            'fooBarBaz',
        ];

        // Should throw the exception.
        $route = new Route($route);
    }
}
