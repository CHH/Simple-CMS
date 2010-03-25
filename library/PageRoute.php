<?php

class PageRoute 
  extends Zend_Controller_Router_Route_Abstract
  implements Spark_UnifiedConstructorInterface
{

  private $_paramDelimiter = "/";

  private $_controllerName = "Page";

  private $_actionName = "view";

  private $_defaultPage = "index";
  
  public static function getInstance(Zend_Config $config)
  {
    return new self($config);
  }

  public function __construct($options = null)
  {
    if(!is_null($options)) {
      $this->setOptions($options);
    }
  }

  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
  }

  public function match($request, $partial = false)
  {
    $params = array();
    $path = $request->getRequestUri();

    $path = trim($path, $this->_paramDelimiter);

    if($path == null)
    {
      $path = $this->_defaultPage;
    }
       
    $params = explode($this->_paramDelimiter, $path);

    if($params === false) {
      return false;
    }
    
    $request->setCommandName($this->_controllerName);
    $request->setActionName($this->_actionName);
    
    $request->setParams($params);
    
    return $params;
  }

  public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
  {
    return null;
  }

  public function setControllerName($name)
  {
    $this->_controllerName = $name;
    return $this;
  }

  public function setActionName($name)
  {
    $this->_actionName = $name;
    return $this;
  }

  public function setDefaultPage($default)
  {
    $this->_defaultPage = $default;
    return $this;
  }  
}
