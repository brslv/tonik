<?php

namespace Tonik;

use Tonik\Entities\Url;
use Tonik\Configurations\Index;
use Tonik\Entities\RouteComponent;
use Tonik\RoutesCollection;

/**
 * Class Dispatcher
 *
 * @package Tonik.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class Dispatcher
{
    /** @var string The url to be dispatched. */
    private $url;

    /** @var string The request method (GET, POST etc.) */
    private $method;

    /** @var array Responsible for keeping all the route's parameters, passed through the url. */
    private $params = [];

    /** @var array RoutesCollection */
    private $routesCollection;

    /**
     * Constructor.
     *
     * @param string $url The url to be dispatched.
     * @param $method The request method (GET, POST etc.).
     */
    public function __construct($url, $method)
    {
        // $this->setUrl($url);
        $this->url = new Url($url);
        $this->method = $method;
    }

    /**
     * Injects the routes collection to be used in the
     * dispatching process by the dispatcher.
     *
     * @param RoutesCollection $routesCollection
     *
     * @return $this
     */
    public function setRoutesCollection(RoutesCollection $routesCollection)
    {
        $this->routesCollection = $routesCollection;

        return $this;
    }

    /**
     * Checks if a RoutesCollection object is set and
     * defers to self::perform().
     *
     * @throws \Exception
     *
     * @return array The result of the dispatching process.
     */
    public function dispatch()
    {
        if (null === $this->routesCollection) {
            throw new \Exception('Are you sure you\'ve injected a RoutesCollection object through the Dispatcher::setRoutesCollection method?');
        }

        return $this->perform();
    }

    /**
     * Performs the actual dispatching.
     *
     * @return array|null The information for the matched route (null if nothing matched).
     */
    private function perform()
    {
        $matchedRoute = null;
        $routes = $this->routesCollection->routes();
        $urlComponents = $this->url->components();

        foreach ($routes[$this->method] as $route) {
            $routeComponents = $route->urlComponents();
            $componentIndex = -1;
            $routeMatches = true;

            if (count($routeComponents) < count($urlComponents) && ! $route->containsEagerComponent()) {
                continue;
            }

            foreach ($routeComponents as $routeComponent) {
                $componentIndex++;

                if (isset($urlComponents[$componentIndex])) {
                    $urlComponent = $urlComponents[$componentIndex];
                } elseif (! isset($urlComponents[$componentIndex]) && $routeComponent->isEager()) {
                    $routeMatches = false;

                    break;   
                } else {
                    $routeMatches = ! $routeComponent->isParameter() || $routeComponent->isOptional();

                    break;
                }

                if ($routeComponent->isEager()) {
                    $routeMatches = true;

                    break;
                }

                if ( ! $routeComponent->isParameter() && $routeComponent->get() == $urlComponent->get()) {
                    continue;
                } 

                // Here we make a simple check if a route component contains a regex constraint
                // and if the url component matches this constraint.
                if (
                    isset($urlComponent) 
                    && $routeComponent->isPattern() 
                    && ! $routeComponent->matchesPattern($urlComponent->get())
                ) {
                        $routeMatches = false;

                        break;
                }
                
                if ($routeComponent->isCompulsory() || $routeComponent->isOptional()) {
                    $this->params[$routeComponent->getNormalized()] = $urlComponent->get();
                } else {
                    $routeMatches = false;

                    break;
                }
            } // End of traversing the route components.

            // var_dump('---');
            if ($routeMatches) {
                $matchedRoute = $route;

                break;
            }

        } // End of traversing the routes.

        if ( ! $matchedRoute) {
            return null;
        }

        $routeInformation = $this->bundleRouteInformation($matchedRoute);

        return $routeInformation;
    }

    /**
     * Bundle the route information for the matched route.
     *
     * @param Route $matchedRoute
     *
     * @return array A simple array, containing the information for the matched route
     */
    private function bundleRouteInformation($matchedRoute)
    {
        $routeInformation = [
            'handler' => $matchedRoute->handler(),
            'params' => $this->params,
            'routeName' => $matchedRoute->name() ? $matchedRoute->name() : null,
        ];

        return $routeInformation;
    }
}
