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
class Sample_DefaultCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $response->appendBody("Hello from Sample Plugin");
  }
  
}
