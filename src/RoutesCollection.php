<?php

namespace Borislav\Broute;

use InvalidArgumentException;
use Borislav\Broute\Entities\Route;
use Borislav\Broute\Configurations\Index;

/**
 * Class RouteCollection
 *
 * @package Borislav\Broute.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class RoutesCollection 
{
    /** @var array Contains all the app's routes */
    private $routes = [];

    /**
     * Constructor.
     *
     * @param array $routes An array of routes.
     */
    public function __construct(array $routes)
    {
        $this->allowedMethods = include(__DIR__ . '/Configurations/methods.php');

        $this->prepareCollection()->spreadRoutes($routes);
    }

    /**
     * Fills up the routes array with initial values (empty arrays).
     *
     * @return $this
     */
    private function prepareCollection()
    {
        foreach ($this->allowedMethods as $allowedMethod) {
            $this->routes[$allowedMethod] = [];
        }

        return $this;
    }

    /**
     * Itterates over the array of routes and registers each one.
     * 
     * @param array $routes The array of routes.
     *
     * @return $this
     */
    private function spreadRoutes(array $routes)
    {
        foreach ($routes as $route) {
            $this->registerRoute($route);
        }

        return $this;
    }

    /**
     * The registration process is simply pushing each route
     * to the array of routes. 
     *
     * @param array $route A single route to be registered.
     *
     * @return $this
     */
    private function registerRoute($routeData)
    {
        $route = new Route($routeData);

        foreach ($route->methods() as $method) {
            $this->routes[$method][] = $route;
        }

        return $this;
    }

    /**
     * Get all the collected routes.
     *
     * @return array
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * Get the route information for a given route name.
     *
     * @param string $routeName The name of the route.
     *
     * @throws InvalidArgumentException If the $routeName is invalid.
     *
     * @return array Information about the route.
     */
    public function route($routeName)
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route) {
                if ($route[Index::NAME] == $routeName) {
                    return $route;
                }
            }
        }

        // No route with this name found - throw an exception.
        throw new InvalidArgumentException("Invalid route name: [{$routeName}]");
    }
}
