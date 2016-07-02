<?php

namespace Borislav\Broute\Entities;

/**
 * Class Url
 *
 * @package Borislav\Broute
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class Url 
{
    /** @var array The components of the url */
    private $components;

    /** @var string */
    private $url;

    /**
     * Url constructor.
     *
     * @param $url The url to be wrapped.
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->url = $this->processUrl();
    }

    /**
     * Process the url.
     *
     * @return string The processed url.
     */
    private function processUrl()
    {
        return 
            $this->cutOffQueryString()
            ->normalize()
            ->get();
    }

    /**
     * Remove the query string from the url.
     *
     * @return $this
     */
    private function cutOffQueryString()
    {
        $queryStringStartIndex = strpos($this->url, '?');

        $this->url = ($queryStringStartIndex) 
            ? substr($this->url, 0, $queryStringStartIndex) 
            : $this->url;

        return $this;
    }

    /**
     * Normalizes a url (deals with slashes).
     *
     * @return $this
     */
    private function normalize()
    {
        $this->url = '/' . ltrim($this->url, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->url;
    }

    /**
     * Get the url components.
     *
     * @return array
     */
    public function components()
    {
        $components = preg_split('/\//', $this->url, null, PREG_SPLIT_NO_EMPTY);

        if (empty($components)) {
            $components = ['/'];
        }

        foreach ($components as $component) {
            $this->components[] = new UrlComponent($component);
        }

        return $this->components;
    }
}
