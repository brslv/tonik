<?php

namespace Tonik\Entities;

/**
 * Class UrlComponent
 *
 * @package Tonik\Entities.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class UrlComponent
{
    /** @var string */
    private $component;

    /**
     * UrlComponent constructor.
     *
     * @param $component
     */
    public function __construct($component)
    {
        $this->setUrlComponent($component);
    }

    /**
     * Set the url component.
     *
     * @param string $component
     *
     * @return $this
     */
    private function setUrlComponent($component)
    {
        $this->component = $component;
    }

    /**
     * Get the url component.
     *
     * @return string
     */
    public function get()
    {
        return $this->component;
    }
}
