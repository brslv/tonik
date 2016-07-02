<?php

namespace Borislav\Broute;

use Borislav\Broute\Entities\Route;
use Borislav\Broute\RoutesCollection;

/**
 * Class Resolver
 *
 * @package Borislav\Broute\Resolver.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class Resolver 
{
    /** @var RoutesCollection */
    private $routesCollection;

    /**
     * Constructor.
     *
     * @param RoutesCollection $routesCollection
     */
    public function __construct(routesCollection $routesCollection)
    {
        $this->routesCollection = $routesCollection;
    }

    /**
     * Get the URL for a given routeName
     * 
     * @TODO: Make this functionality.
     */
    public function resolve($routeName, $params = [])
    {
        foreach ($this->routesCollection->routes() as $method => $routes) {
            foreach ($routes as $route) {
                if ($route->name() === $routeName) {
                    return $this->resolveRoute($route, $params);
                }
            }
        }
    }

    /**
     * Resolve a route.
     *
     * @param Route $route
     * @param array $params
     * 
     * @return string|bool
     */
    private function resolveRoute(Route $route, $params = [])
    {
        $url = [];

        foreach ($route->urlComponents() as $component) {
            $normalized = $component->getNormalized();

            if ( ! $component->isParameter()) {
                $url[] = $normalized;

                continue;
            }

            if ( ! array_key_exists($normalized, $params) && $component->isCompulsory()) {
                return false;
            }

            $url[] = strtolower($params[$normalized]);
        }

        return '/' . ltrim(implode('/', $url), '/');
    }
}
