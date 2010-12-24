<?php

namespace Plugin\Pages;

use Spark\Util\Options,
    Spark\HttpRequest,
    Spark\Router\Exception,
    Spark\Router\Route;

class PageRoute implements Route
{
    const PARAM_DELIMITER = "/";

    private $callback;
    
    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }
    
    function setOptions(array $options)
    {
        Options::setOptions($this, $options);
        return $this;
    }
    
    function match(HttpRequest $request)
    {
        $path = $request->getRequestUri();
        $path = trim($path, self::PARAM_DELIMITER);
        
        // Strip query params
        if (false !== ($pos = strpos($path, "?"))) {
            $path = substr($path, 0, $pos);
        }
        
        if (false === Page::find($path)) {
            $request->setMetadata("page", $path);
            throw new Exception("Page not found", 404);
        }
        return array("page" => $path, "callback" => $this->callback);
    }
    
    function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }
}
