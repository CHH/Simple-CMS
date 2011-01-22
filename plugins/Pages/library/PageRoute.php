<?php

namespace Plugin\Pages;

use Spark\HttpRequest,
    Spark\Router\Exception,
    Spark\Router\Route;

class PageRoute implements Route
{
    const PARAM_DELIMITER = "/";

    private $callback;
    
    function __construct($callback)
    {
        $this->callback = $callback;
    }
    
    function __invoke(HttpRequest $request)
    {
        $path = $request->getRequestUri();
        $path = trim($path, self::PARAM_DELIMITER);
        
        // Strip query params
        if (false !== ($pos = strpos($path, "?"))) {
            $path = substr($path, 0, $pos);
        }
        $request->meta("page", $path);
        
        if (false === Page::find($path)) {
            return false;
        }
        return $this->callback;
    }
}
