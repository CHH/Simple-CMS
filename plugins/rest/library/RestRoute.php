<?php

class RestRoute 
  extends Zend_Controller_Router_Route_Abstract
  implements Spark_UnifiedConstructorInterface
{
  const PARAM_DELIMITER = "/";
  
  protected $_pageMapper = null;
  
  public function __construct($options = null)
  {
    $this->setOptions($options);
  }
  
  public function match($request)
  {
    $path = trim($request->getRequestUri(), self::PARAM_DELIMITER);
    
    $params = explode(self::PARAM_DELIMITER, $path);
    
    if($params[0] === "api") {
      unset($params[0]);
      $path = join($params, self::PARAM_DELIMITER);
      
      $request->setCommandName("default");
      $request->setModuleName("rest");
      $request->setParam("path", $path);
      
      return array("command"=>"default", "module"=>"rest", "path"=>$path);
    } else {
      return false;
    }
  }
  
  public function assemble($data = array(), $reset = false, $encode = false)
  {}
  
  public static function getInstance(Zend_Config $config)
  {
    return new self($config);
  }
  
  public function setPageMapper(PageMapper $pageMapper)
  {
    $this->_pageMapper = $pageMapper;
  }
  
  public function getPageMapper()
  {
    if(is_null($this->_pageMapper)) {
      $this->_pageMapper = new PageMapper;
    }
    return $this->_pageMapper;
  }
  
  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }
}