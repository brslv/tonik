<?php

namespace Borislav\Broute\Entities;

use InvalidArgumentException;
use Borislav\Broute\Configurations\Index;
use Borislav\Broute\Entities\RouteComponent;

/**
 * Class Route
 *
 * @package Borislav\Broute.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class Route 
{
    /** @var array */
    private $methods;

    /** @var string */
    private $url;

    /** @var string */
    private $handler;

    /** @var string */
    private $name;

    /** @var array */
    private $urlComponents;

    /** @var The allowed HTTP request methods. */
    private $allowedMethods;

    /** @var bool if the route has * component. */
    private $containsEagerComponent = false;

    /**
     * Constructor.
     *
     * @param array $routeData The data for the route.
     */
    public function __construct(array $routeData)
    {
        $this->allowedMethods = include(__DIR__ . '/../Configurations/methods.php');

        $this->recognize($routeData);
    }

    /**
     * Recognize the parts of a route.
     *
     * @param array $route A route array.
     *
     * @return $this
     */
    private function recognize($routeData)
    {
        // Recognize methods.
        $routeMethods = array_shift($routeData);
        $this->methods = [strtoupper($routeMethods)];

        if ($this->respondsToMany($this->methods[0])) {
            $this->methods = explode('|', $this->methods[0]);
        }

        foreach ($this->methods as $method) {
            if ( ! $this->valid($method)) {
                $allowedMethods = implode(', ', $this->allowedMethods);

                throw new InvalidArgumentException("Method [{$method}] seemd to be invalid. Allowed methods: [{$allowedMethods}].");
            }
        }

        // Recognize url.
        $this->url = $routeData[Index::URL];

        // Recognize handler.
        $this->handler = $routeData[Index::HANDLER];

        // Recognize name.
        $this->name = null;

        if (isset($routeData[Index::NAME])) {
            $this->name = $routeData[Index::NAME];
        }

        return $this;
    }

    /**
     * Checks if the route has a * component (eager route).
     *
     * @return bool
     */
    public function containsEagerComponent() {
        return $this->containsEagerComponent;
    }

    /**
     * Checks if a given route can redpond to many methods.
     * It's designated by a delimiter, such as |POST etc.
     *
     * @param string $methods  or |POST or something in this fashion.
     *
     * @return bool
     */
    private function respondsToMany($methods)
    {
        return false !== strpos($methods, '|');
    }

    /**
     * Check if the method is a valid HTTP method.
     *
     * @param string $method
     *
     * @return bool
     */
    private function valid($method)
    {
        return in_array($method, $this->allowedMethods);
    }

    /**
     * Get the url components from the route.
     *
     * @return array
     */
    public function urlComponents()
    {
        $components = preg_split('/\//', $this->url, null, PREG_SPLIT_NO_EMPTY);

        if (empty($components)) {
            $components = ['/'];
        }

        foreach ($components as $component) {
            $routeComponent = new RouteComponent($component);
            $this->urlComponents[] = $routeComponent;

            if ($routeComponent->isEager()) {
                $this->containsEagerComponent = true;
            }
        }

        return $this->urlComponents;
    }

    /**
     * Get the HTTP methods.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Get the url.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Get the handler.
     *
     * @return string
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
