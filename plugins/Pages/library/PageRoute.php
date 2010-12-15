<?php

namespace Plugin\Pages;

use Spark\Options,
    Spark\Controller\Exception,
    Spark\Controller\HttpRequest;

class PageRoute implements \Spark\Router\Route
{
    const PARAM_DELIMITER = "/";

    private $callback;

    private $defaultPage;
    
    private $defaults = array(
        "default_page" => "index"
    );

    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    function setOptions(array $options)
    {
        Options::setOptions($this, $options, $this->defaults);
        return $this;
    }

    function match(HttpRequest $request)
    {
        $path = $request->getRequestUri();
        $path = trim($path, self::PARAM_DELIMITER);

        if ($path == null) {
            $path = $this->defaultPage;
        }
        
        if (false === Page::find($path)) {
            $request->setParam("page", $path);
            throw new Exception("Page not found", 404);
        }
        
        return array("page" => $path, "__callback" => $this->callback);
    }
    
    function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }
    
    function setDefaultPage($default)
    {
        $this->defaultPage = $default;
        return $this;
    }  
}
