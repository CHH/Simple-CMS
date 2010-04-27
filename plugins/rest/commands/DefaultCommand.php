<?php

class Rest_DefaultCommand implements Spark_Controller_CommandInterface
{
  protected $_pageMapper;
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $thisPlugin = Spark_Registry::get("Plugins")->rest;
    $thisPlugin->layout->disableLayout();
    $config = $thisPlugin->getConfig();
    
    $method = strtolower($request->getMethod());
    
    if(in_array($method, array("post", "put", "delete"))) {
      /* Check if api key is valid */
      $requestKey = $request->getParam("apikey");
      
      $keys = array();
      
      foreach($config->api_secret as $app => $secret) {
        $keys[] = md5($app . $secret);
      }
      
      if(!in_array($requestKey, $keys)) {
        header("HTTP/1.0 403 Forbidden");
        return;
      }
    }
    
    $action = $method . "Action";
    
    if(!method_exists($this, $action)) {
      header("HTTP/1.0 404 Not Found");
      return;
    }
    
    $this->$action($request, $response);
  }
  
  public function getAction($request, $response)
  {
    $page = $this->_getPageMapper()->find($request->getParam("path"));
    
    $response->setBody($page->toJson());
  }
  
  public function postAction($request, $response)
  {
    $response->setBody("POST Action");
  }
  
  public function putAction($request, $response)
  {
    $response->setBody("PUT Action");
  }
  
  public function deleteAction($request, $response)
  {
    $response->setBody("DELETE Action");
  }
  
  protected function _getPageMapper()
  {
    if(is_null($this->_pageMapper)) {
      $this->_pageMapper = new PageMapper;
    }
    return $this->_pageMapper;
  }
  
}
