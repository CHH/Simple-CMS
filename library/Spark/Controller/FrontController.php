<?php

class Spark_Controller_FrontController implements Spark_UnifiedConstructorInterface
{
  
  protected $_request = null;
  
  protected $_response = null;
  
  protected $_resolver = null;
  
  protected $_router = null;
  
  protected $_preFilters;
  protected $_postFilters;
  
  private $_filterClass = "Spark_Controller_FilterInterface";
  
  private $_errorCommandName = "Error";
  
  public function __construct($options = null)
  {
    $this->_preFilters = new Spark_FilterChain;
    $this->_postFilters = new Spark_FilterChain;
    
    $this->_preFilters->accept($this->_filterClass);
    $this->_postFilters->accept($this->_filterClass);
    
    if(!is_null($options)) {
      $this->setOptions($options);
    }
  }
  
  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }
  
  public function handleRequest()
  {
    $request = $this->getRequest();
    $response = $this->getResponse();
    
    $this->_preFilters->process($request, $response);
    
    $this->getRouter()->route($request);
    
    try {
      $command = $this->getResolver()->getCommand($request);
  
      if($command) {
          $command->execute($request, $response);
  
      } else {
        // Call the Error Command
        $request->setParam("code", 404);
        $this->getResolver()->getCommandByName($this->_errorCommandName)
             ->execute($request, $response);
      }
    
    } catch(Exception $e) {
      $request->setParam("code", 500);
      $request->setParam("exception", $e);

      $this->getResolver()->getCommandByName($this->_errorCommandName)
           ->execute($request, $response);
    }
    
    $this->_postFilters->process($request, $response);
    
    $response->sendResponse();
  }
  
  public function handleException(Exception $e) 
  {
    $errorCommand = $this->getResolver()->getCommandByName($this->_errorCommandName);
    
    $request = $this->getRequest();
    $response = $this->getResponse();
    
    $request->setParam("code", $e->getCode());
    $request->setParam("exception", $e);
    
    $errorCommand->execute($request, $response);
    
    $this->_postfilters->process($request, $response);
  }
  
  public function addPreFilter(Spark_Controller_FilterInterface $filter)
  {
    $this->_preFilters->add($filter);
    return $this;
  }

  public function addPostFilter(Spark_Controller_FilterInterface $filter)
  {
    $this->_postFilters->add($filter);
    return $this;
  }
  
  public function getRequest()
  {
    if(is_null($this->_request)) {
      $this->_request = Spark_Object_Manager::create("Spark_Controller_HttpRequest");
    }
    return $this->_request;
  }
  
  public function setRequest(Zend_Controller_Request_Abstract $request)
  {
    $this->_request = $request;
    return $this;
  }
  
  public function getResponse()
  {
    if(is_null($this->_response)) {
      $this->_response = Spark_Object_Manager::create("Spark_Controller_HttpResponse");
    }
    return $this->_response;
  }
  
  public function setResponse(Zend_Controller_Response_Abstract $response)
  {
    $this->_response = $response;
    return $this;
  }
  
  public function getResolver()
  {
    if(is_null($this->_resolver)) {
      $this->_resolver = Spark_Object_Manager::create("Spark_Controller_CommandResolver");
    }
    return $this->_resolver;
  }
  
  public function setResolver(Spark_Controller_CommandResolverInterface $resolver)
  {
    $this->_resolver = $resolver;
    return $this;
  }
  
  public function getRouter()
  {
    if(is_null($this->_router)) {
      $this->_router = Spark_Object_Manager::create("Zend_Controller_Router_Rewrite");
    }
    return $this->_router;
  }
  
  public function setRouter(Zend_Controller_Router_Interface $router) {
    $this->_router = $router;
    return $this;
  }
  
  public function setFilterClass($filterClass)
  {
    $this->_filterClass = $filterClass;
    return $this;
  }
}