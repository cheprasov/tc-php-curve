<?php

namespace TC\Curve\Request;

class Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    // and so on

    /**
     * @var null|array
     */
    protected $routeVars;

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET;
    }

    /**
     * @param array|null $routeVars
     */
    public function setRouteVars(array $routeVars = null)
    {
        $this->routeVars = $routeVars;
    }

    /**
     * @return array|null
     */
    public function getRouteVars()
    {
        return $this->routeVars;
    }
}
