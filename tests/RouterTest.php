<?php

use Borislav\Broute\Router;
use Borislav\Broute\Dispatcher;
use Borislav\Broute\RoutesCollection;

class RouterTest extends PHPUnit_Framework_TestCase 
{
    public function setUp()
    {
        $this->routes = [
            ['GET', '/', 'HomeController@index', 'home'],
            ['GET', '/about', 'PagesController@about', 'about'],
            ['GET', '/about/{name}', 'PagesController@somebody', 'aboutSomebody'],
            ['GET', '/bar/{baz?}', 'AcmeController@bar', 'bar'],
        ];
    }
    
    public function testItMatchesSlash()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'HomeController@index',
            'params' => [],
            'routeName' => 'home',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItDoesntMatchNonexistingRoute()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/foo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null; // No matched route.

        $this->assertEquals($expect, $match);

        /** --- */
        $routesCollection = new RoutesCollection([
            ['GET', '/about', 'About', 'about'],
        ]);
        $dispatcher = new Dispatcher('/', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null; // No matched route.

        $this->assertEquals($expect, $match);
    }

    public function testItMatchesStar()
    {
        $routesCollection = new RoutesCollection([
            ['GET', '/foo/*', 'Ertin', 'ertin'],
        ]);
        $dispatcher = new Dispatcher('/foo/bar/baz/sam/john/bob', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'Ertin',
            'params' => [],
            'routeName' => 'ertin',
        ];

        $this->assertEquals($expect, $match);

        /** --- */
        $dispatcher = new Dispatcher('/foo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;

        $this->assertEquals($expect, $match);

        /** --- */
        $dispatcher = new Dispatcher('/boo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;

        $this->assertEquals($expect, $match);

        /** --- */
        $routesCollection = new RoutesCollection([
            ['DELETE', '/baz/fuzz/*', 'BazzFuzz', 'bazzfuzz'],
            ['DELETE', '/baz/fuzz/{test?}', 'BazzFuzzTest', 'bazzfuzztest'],
        ]);
        $dispatcher = new Dispatcher('/baz/fuzz/something', 'DELETE');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'BazzFuzz',
            'params' => [],
            'routeName' => 'bazzfuzz',
        ];

        $this->assertEquals($expect, $match);

        /** --- */
        $dispatcher = new Dispatcher('/baz/fuzz/', 'DELETE');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'BazzFuzzTest',
            'params' => [],
            'routeName' => 'bazzfuzztest',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItMatchesWithoutStartingSlash()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PagesController@about',
            'params' => [],
            'routeName' => 'about',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItCanMatchSimpleRouteWithoutParameters()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PagesController@about',
            'params' => [],
            'routeName' => 'about',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about/', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PagesController@about',
            'params' => [],
            'routeName' => 'about',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItMatchesSimpleRouteWithCompulsoryParameter()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about/john-doe', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PagesController@somebody',
            'params' => [
                'name' => 'john-doe',
            ],
            'routeName' => 'aboutSomebody',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItMatchesSimpleRouteWithOptionalParameter()
    {
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/bar/hey-hey-ho', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'AcmeController@bar',
            'params' => [
                'baz' => 'hey-hey-ho',
            ],
            'routeName' => 'bar',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/bar', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'AcmeController@bar',
            'params' => [],
            'routeName' => 'bar',
        ];

        $this->assertEquals($expect, $match);
    }

    public function testItFailsOnCompulsoryParameters()
    {
        $routesCollection = new RoutesCollection([
            ['GET', '/foo/{name}', 'PersonController@introduce', 'introduce'],
        ]);

        $dispatcher = new Dispatcher('/foo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;
        $this->assertEquals($expect, $match);
    }

    public function testComplexRoutes()
    {
        $routesCollection = new RoutesCollection([
            ['GET', '/foo/{name}/{surname}/{lastName}/{age}/{gender?}', 'PersonController@introduce', 'introduce'],
        ]);

        $dispatcher = new Dispatcher('/foo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;
        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/John/Parker/Doe/30/male', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PersonController@introduce',
            'params' => [
                'name' => 'John',
                'surname' => 'Parker',
                'lastName' => 'Doe',
                'age' => '30',
                'gender' => 'male',
            ],
            'routeName' => 'introduce',
        ];
        $this->assertEquals($expect, $match);
    }

    public function testRequestMethods()
    {
        $routesCollection = new RoutesCollection([
            ['POST', '/foo', 'FooController@postFoo', 'postFoo'],
            ['DELETE', '/foo', 'FooController@deleteFoo', 'deleteFoo'],
        ]);
        $dispatcher = new Dispatcher('/foo', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;
        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo', 'OPTIONS');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;
        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo', 'POST');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'FooController@postFoo',
            'params' => [],
            'routeName' => 'postFoo',
        ];
        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo', 'DELETE');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'FooController@deleteFoo',
            'params' => [],
            'routeName' => 'deleteFoo',
        ];
        $this->assertEquals($expect, $match);
    }

    /**
     * @group resolving
     */
    public function testItGeneratesTheProperUrlFromRouteName()
    {
        
        // ['GET', '/', 'HomeController@index', 'home'],
        // ['GET', '/about', 'PagesController@about', 'about'],
        // ['GET', '/about/{name}', 'PagesController@somebody', 'aboutSomebody'],
        // ['GET', '/bar/{baz?}', 'AcmeController@bar', 'bar'],

        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $url = $router->url('about');

        $expect = '/about';

        $this->assertEquals($expect, $url);

        /** --- */
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $url = $router->url('bar', ['baz' => 'cheeze']);

        $expect = '/bar/cheeze';

        $this->assertEquals($expect, $url);

        /** --- */
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $url = $router->url('home', ['baz' => 'cheeze']);

        $expect = '/';

        $this->assertEquals($expect, $url);

        /** --- */
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        // ['GET', '/about/{name}', 'PagesController@somebody', 'aboutSomebody'],
        $url = $router->url('aboutSomebody', ['name' => 'John']);

        $expect = '/about/john';

        $this->assertEquals($expect, $url);

        /** --- */
        $routesCollection = new RoutesCollection($this->routes);
        $dispatcher = new Dispatcher('/about', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        // ['GET', '/about/{name}', 'PagesController@somebody', 'aboutSomebody'],
        $url = $router->url('nonExisting', ['foo' => 'bar']);

        $expect = false;

        $this->assertEquals($expect, $url);
    }

    /**
     * @group patterns
     */
    public function testItRecognizesPatternAliases()
    {
        $routesCollection = new RoutesCollection([
            ['GET', 'foo/{word:(s)}', 'WordController@string', 'stringWord'],
            ['GET', 'foo/{word:(n)}', 'WordController@numeric', 'numericWord'],
            // ['GET', 'foo/bar/{word:(0-9a-zA-Z)}', 'WordController@regex'],
        ]);
        $dispatcher = new Dispatcher('/foo/bar', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'WordController@string',
            'params' => [
                'word' => 'bar',
            ],
            'routeName' => 'stringWord',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/123', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'WordController@numeric',
            'params' => [
                'word' => '123',
            ],
            'routeName' => 'numericWord',
        ];

        $this->assertEquals($expect, $match);
    }

    /**
     * @group patterns
     */
    public function testItRecognizesPatterns()
    {
        $routesCollection = new RoutesCollection([
            ['GET', 'foo/{word:([ab])}', 'LettersController@aOrB', 'aOrB'],
            ['POST', 'foo/{slug:( [a-z]+-[a-z]+-[012]+ )?}', 'PatternsController@complex', 'complex'],
        ]);
        $dispatcher = new Dispatcher('/foo/a', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'LettersController@aOrB',
            'params' => [
                'word' => 'a',
            ],
            'routeName' => 'aOrB',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/b', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'LettersController@aOrB',
            'params' => [
                'word' => 'b',
            ],
            'routeName' => 'aOrB',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/c', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null; 

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/john-doe-1', 'POST');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PatternsController@complex',
            'params' => [
                'slug' => 'john-doe-1',
            ],
            'routeName' => 'complex',
        ];

        $this->assertEquals($expect, $match);
    }

    /**
     * @group patterns
     */
    public function testItRecognizesPatternsOnOptionalParameters()
    {
        $routesCollection = new RoutesCollection([
            ['GET', 'foo/{word:([ab])}', 'LettersController@aOrB', 'aOrB'],
            ['POST', 'foo/{slug:(  ([a-z]+)-([a-z]+)-([012]+)  )?}', 'PatternsController@complex', 'complex'],
        ]);

        $dispatcher = new Dispatcher('/foo', 'POST');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PatternsController@complex',
            'params' => [],
            'routeName' => 'complex',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/some-bar-2', 'POST');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = [
            'handler' => 'PatternsController@complex',
            'params' => [
                'slug' => 'some-bar-2',
            ],
            'routeName' => 'complex',
        ];

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/some-bar-4', 'POST');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;

        $this->assertEquals($expect, $match);

        /** --- */

        $dispatcher = new Dispatcher('/foo/some-bar-2', 'GET');
        $router = new Router($routesCollection, $dispatcher);

        $match = $router->run();

        $expect = null;

        $this->assertEquals($expect, $match);
    }
}
