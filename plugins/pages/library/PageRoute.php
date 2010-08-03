<?php

class PageRoute 
  extends Zend_Controller_Router_Route_Abstract
  implements Spark_UnifiedConstructorInterface
{
  
  const PARAM_DELIMITER = "/";
  
  private $_controllerName;

  private $_actionName;
  
  private $_moduleName;
  
  private $_defaultPage;
  
  private $_defaults = array(
    "controller_name" => "Page",
    "action_name"     => "view",
    "module_name"     =>  null,
    "default_page"    => "index"
  );
  
  public static function getInstance(Zend_Config $config)
  {
    return new self($config);
  }

  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }

  public function setOptions(array $options)
  {
    Spark_Options::setOptions($this, $options, $this->_defaults);
    return $this;
  }

  public function match($request)
  {
    $params = array();
    $path   = $request->getRequestUri();
    $path   = substr($path, strlen($request->getBaseUrl()));
    
    $path   = trim($path, self::PARAM_DELIMITER);

    if ($path == null) {
      $path = $this->_defaultPage;
    }
    
    if (!$page = Page::find($path)) {
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
  {}
  
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

