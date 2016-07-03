<?php

namespace Tonik\Entities;

/**
 * Class RouteComponent
 *
 * @package Tonik\Entities.
 * @author Borislav Grigorov <borislav.b.grigorov@gmail.com>
 */
class RouteComponent
{
    /** @const The opening token for a pattern */
    const PATTERN_OPENING_TOKEN = '(';

    /** @const The closing token for a pattern */
    const PATTERN_CLOSING_TOKEN = ')';

    /** @const Designates a component is eager (matches the rest of the url) */
    const EAGER_COMPONENT_TOKEN = '*';

    /** @var string */
    private $component;

    /** @var array The available pattern aliases */
    private $patternAliases = [
        'n' => '#^[0-9]+$#',
        's' => '#^[a-zA-Z]+$#',
    ];

    /**
     * Constructor.
     *
     * @param string $component The route component.
     */
    public function __construct($component)
    {
        $this->component = $component;
    }

    public function isEager()
    {
        return $this->component == self::EAGER_COMPONENT_TOKEN;
    }

    public function isParameter()
    {
        return (
            substr($this->component, 0, 1) === '{'
            && substr($this->component, -1) === '}'
        );
    }

    /**
     * Checks if a parameter is optional ( {exampleOptional?} ).
     *
     * @return bool
     */
    public function isOptional()
    {
        return (
            substr($this->component, 0, 1) === '{' 
            && substr($this->component, -2) === '?}' 
        );
    }

    /**
     * Checks if a parameter is compulsory ( {exampleCompulsory} ).
     *
     * @return bool
     */
    public function isCompulsory()
    {
        return (
            substr($this->component, 0, 1) === '{' 
            && substr($this->component, -1) === '}' 
            && substr($this->component, -2) !== '?}'
        );
    }

    /**
     * Checks if a parameter has a pattern constraint.
     *
     * @return bool
     */
    public function isPattern() 
    {
        return (
            strpos($this->component, self::PATTERN_OPENING_TOKEN) != false
            && strpos($this->component, self::PATTERN_CLOSING_TOKEN) != false
        );
    }

    /**
     * Checks if a given url component matches route's regex constraints.
     *
     * @return bool
     */
    public function matchesPattern($urlComponentString) {
        $openTagStart = strpos($this->component, self::PATTERN_OPENING_TOKEN) + 1;
        $closeTagStart = strripos($this->component, self::PATTERN_CLOSING_TOKEN) - $openTagStart;

        $pattern = trim(substr($this->component, $openTagStart, $closeTagStart));

        if (array_key_exists($pattern, $this->patternAliases)) {
            $pattern = $this->patternAliases[$pattern];
        } else {
            $pattern = '#^' . $pattern . '$#';
        }

        preg_match($pattern, $urlComponentString, $match);

        return ! empty($match);
    }

    /**
     * Get the component as a string.
     *
     * @return string
     */
    public function get()
    {
        return $this->component;
    }

    /**
     * Get the normalized component as a string (without the {} and the  {?}).
     *
     * @return bool
     */
    public function getNormalized()
    {
        $normalized = ltrim(rtrim($this->component, '}'), '{');
        $normalized = rtrim($normalized, '?');

        if ($this->isPattern()) {
            $normalized = substr($normalized, 0, strpos($normalized, ':'));
        }

        return $normalized;
    }
}
