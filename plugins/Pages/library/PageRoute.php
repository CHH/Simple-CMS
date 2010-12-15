<?php

namespace Plugin\Pages;

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
        \Spark\Options::setOptions($this, $options, $this->defaults);
        return $this;
    }

    function match(\Spark\Controller\HttpRequest $request)
    {
        $path = $request->getRequestUri();
        $path = trim($path, self::PARAM_DELIMITER);

        if ($path == null) {
            $path = $this->defaultPage;
        }

        if (false === Page::find($path)) {
            $request->setParam("page", $path);
            throw new \Spark\Controller\Exception("Page not found", 404);
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
