<?php

namespace Tonik;

use Tonik\Resolver;
use Tonik\Dispatcher;
use Tonik\RoutesCollection;

/**
 * Class Router
 *
 * @package Tonik.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class Router
{
    /** @var RoutesCollection */
    private $routesCollection;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var Resolver */
    private $resolver;

    /**
     * Constructor
     *
     * @param RoutesCollection $routesCollection A RoutesCollection object, containing all the routes.
     * @param Dispatcher A Dispatcher object.
     */
    public function __construct(
        RoutesCollection $routesCollection,
        Dispatcher $dispatcher
    ) {
        $this->routesCollection = $routesCollection;
        $this->dispatcher = $dispatcher;
        $this->resolver = new Resolver($routesCollection);
    }

    /**
     * Matches the url to a given route, using the dispatcher.
     *
     * @return array|null Matched route information (null if nothing matched).
     */
    public function run()
    {
        return $this->dispatcher
            ->setRoutesCollection($this->routesCollection)
            ->dispatch();
    }

    /**
     * Resolves the URL for a given route name.
     *
     * @param string $routeName The name of the route.
     *
     * @return string
     */
    public function url($routeName, $params = [])
    {
        return $this->resolver->resolve($routeName, $params);
    }
}
