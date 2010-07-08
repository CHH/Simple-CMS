<?php

class PageRoute 
  extends Zend_Controller_Router_Route_Abstract
  implements Spark_UnifiedConstructorInterface
{
  
  const PARAM_DELIMITER = "/";
  
  private $_controllerName = "Page";

  private $_actionName = "view";
  
  private $_moduleName;
  
  private $_defaultPage = "index";
  
  public static function getInstance(Zend_Config $config)
  {
    return new self($config);
  }

  public function __construct($options = null)
  {
    $this->setOptions($options);
  }

  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
  }

  public function match($request)
  {
    $params = array();
    $path = $request->getRequestUri();
    $path = substr($path, strlen($request->getBaseUrl()));
    
    $path = trim($path, self::PARAM_DELIMITER);

    if($path == null)
    {
      $path = $this->_defaultPage;
    }
    
    $pageMapper = Spark_Object_Manager::get("PageMapper");
    
    if(!$pageMapper->find($path)) {
      $request->setParam("page", $path);
      return false;
    }
    
    $request->setCommandName($this->_controllerName);
    $request->setActionName($this->_actionName);
    $request->setModuleName($this->_moduleName);
    
    $params["page"] = $path;
    
    return $params;
  }

  public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
  {
    return $data["prefix"] . self::PARAM_DELIMITER . $data["id"];
  }
  
  public function setModuleName($name)
  {
    $this->_moduleName = $name;
    return $this;
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

