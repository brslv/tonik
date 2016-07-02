<?php

namespace Borislav\Broute;

use Borislav\Broute\Entities\Url;
use Borislav\Broute\Configurations\Index;
use Borislav\Broute\Entities\RouteComponent;
use Borislav\Broute\RoutesCollection;

/**
 * Class Dispatcher
 *
 * @package Borislav\Broute.
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
        /** -------------------------------------------------------------------------------------- **
         * The logic here is simple.
         * -
         *
         * Iterating through each route, split it on [/]
         * and compare each route's part (component) with it's coresponding url part (component).
         *
         * If a match is found, stop the iteration. If no - continue iterating.
         *
         * -
         * Details. 
         * -
         * First - iterating through each route, 
         * check if the count of the route's components is less then the count of the url components.
         * If it's less, then there's no reason to check this route further more. It's not the cool guy.
         *
         * Second - iterating through each route's component,
         * check if:
         *      1. It has a corresponding value in the url
         *          > NO: If the current route's component is not a parameter (such as {name} or {name?})
         *                OR is not optional (such as {name?})
         *                THEN stop iterating through the route's components -> this is not our route.
         *
         *      2. The current route's component is NOT parameter and it matches the given url component
         *          
         *      3. If the route's component has a pattern (regex) constraint - check it.
         *
         *      4. Finally - if the route is compulsory or optional - put the corresponding url value
         *                   in the parameters array.
         *
         * Third - if a route is matched - stop iterating the routes.
         *
         * Fourth - return the matched route information or null (if no route has been matched).
         *
         * That's it.
         ** -------------------------------------------------------------------------------------- */

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
