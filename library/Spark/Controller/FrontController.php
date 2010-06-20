<?php
/**
 * Simple Implementation of a Front Controller.
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Controller
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Controller
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Controller_FrontController implements Spark_UnifiedConstructorInterface
{
  /**
   * @var Spark_Controller_RequestInterface
   */
  protected $_request = null;
  
  /**
   * @var Zend_Controller_Request_Abstract
   */
  protected $_response = null;
  
  /**
   * @var Spark_Controller_CommandResolverInterface
   */
  protected $_resolver = null;
  
  /**
   * @var Zend_Controller_Router_Interface
   */
  protected $_router = null;
  
  /** 
   * @var Spark_Event_Dispatcher
   */
  protected $_eventDispatcher = null;
  
  /**
   * @var string
   */
  private $_filterClass = "Spark_Controller_FilterInterface";
  
  /**
   * @var string
   */
  private $_errorCommand = "Error";
  
  const EVENT_ROUTE_STARTUP = "spark.controller.front_controller.route_startup";
  const EVENT_ROUTE_SHUTDOWN = "spark.controller.front_controller.route_shutdown";
  const EVENT_BEFORE_DISPATCH = "spark.controller.front_controller.before_dispatch";
  const EVENT_AFTER_DISPATCH = "spark.controller.front_controller.after_dispatch";
  
  public function __construct($options = null)
  {
    $this->setOptions($options);
  }
  
  /**
   * setOptions() - Used to set configuration options on the object
   * @param mixed $options Either an array of key value pairs matching the 
   *                       Names of the Setter Methods or an Zend_Config Instance
   *
   * @return Spark_Controller_FrontController
   */
  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }
  
  /**
   * handleRequest() - This is where the heavy lifting in the Front Controller is
   *   done. Routes the request, loads the command, throws an exception if something
   *   goes wronng and triggers events for you to handle them.
   *   Four events are thrown throughout the dispatching cycle, they alle have
   *   the common namespace of "spark.controller.front_controller.":
   *     - route_startup, is called before routing is done
   *     - route_shutdown, routing is done
   *     - before_dispatch, before the command gets loaded and executed
   *     - after_dispatch, after the command was executed and before the response
   *       gets sent to the client
   * 
   * @return void
   */
  public function handleRequest()
  {
    $request = $this->getRequest();
    $response = $this->getResponse();
    
    $eventDispatcher = $this->getEventDispatcher();
    
    $event = new Spark_Controller_Event;
    $event->setRequest($request)->setResponse($response);
    
    $eventDispatcher->trigger(self::EVENT_ROUTE_STARTUP, null, $event);
    
    $this->getRouter()->route($request);
    
    $eventDispatcher->trigger(self::EVENT_ROUTE_SHUTDOWN, null, $event);
    
    try {
      $command = $this->getResolver()->getCommand($request);
      
      $eventDispatcher->trigger(self::EVENT_BEFORE_DISPATCH, null, $event);
      
      if($command) {
        $request->setDispatched(true);
        $command->execute($request, $response);
        
        /** 
         * If the dispatched flag is not true (e.g. if a command forwards 
         * request to another command), do another round in the dispatch loop.
         */
        if(!$request->isDispatched()) {
          $this->handleRequest();
        }
      } else {
        throw new Spark_Controller_Exception("There was no matching command for this
          Request found. Make sure the Command {$request->getCommandName()} exists", 404);
      }
      
      $eventDispatcher->trigger(self::EVENT_AFTER_DISPATCH, null, $event);
  
      $response->sendResponse();
      
    } catch(Exception $e) {
      $this->handleException($e);
    }
    
  }
  
  /**
   * handleException() - Can either be registered as fallback exception handler
   *   with set_exception_handler for more friendly uncatched Exceptions or you can pass
   *   an Exception directly to it, like in the handleRequest Method
   *
   * @param Exception $e
   */
  public function handleException(Exception $e) 
  {
    if(strpos($this->_errorCommand, "::")) {
      $command = explode("::", $this->_errorCommand);
      $errorCommand = $this->getResolver()->getCommandByName($command[1], $command[0]);
    } else {
      $errorCommand = $this->getResolver()->getCommandByName($this->_errorCommand);
    }
    
    $request = $this->getRequest();
    $response = $this->getResponse();
    
    $request->setParam("code", $e->getCode());
    $request->setParam("exception", $e);
    
    $event = new Spark_Controller_Event;
    $event->setRequest($request)->setResponse($response);
    
    $request->setDispatched(true);
    $errorCommand->execute($request, $response);
    
    $this->getEventDispatcher()->trigger("spark.controller.front_controller.after_dispatch", null, $event);
    
    $response->sendResponse();
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
  
  public function setEventDispatcher(Spark_Event_DispatcherInterface $dispatcher)
  {
    $this->_eventDispatcher = $dispatcher;
    return $this;
  }
  
  public function getEventDispatcher()
  {
    if(is_null($this->_eventDispatcher)) {
      throw new Spark_Controller_Exception("Spark Controller requires an "
        . "Event Dispatcher to work. Please supply either an Instance of "
        . "Spark_Event_Dispatcher or an object implementing the "
        . "Spark_Event_DispatcherInterface to the setEventDispatcher() Method");
    }
    return $this->_eventDispatcher;
  }
  
  public function setErrorCommand($command)
  {
    $this->_errorCommand = $command;
    return $this;
  }
  
  public function getErrorCommand()
  {
    return $this->_errorCommand;
  }
}
