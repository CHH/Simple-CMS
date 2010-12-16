<?php

/**
 * This is the Default Command of the plugin. When the Plugin Identifier is 
 * given in the request but not the command parameter then an instance of
 * this class is created and the Front Controller passes the 
 * request and response object to its execute() Method as arguments. 
 *
 * This is the lightweight approach to do some MVC in your Plugin and
 * should be performance wise superior to the Action-Controller-based
 * approach, similar to Zend_Controller_Action, which is also available.
 */
namespace Plugin\Sample\Controller; 

use Spark\Controller\HttpRequest, Spark\Controller\HttpResponse;

class IndexController implements \Spark\Controller\Controller
{

    function __invoke(HttpRequest $request, HttpResponse $response)
    {
        $response->appendBody("Hello from Sample Plugin");
    }
  
}
