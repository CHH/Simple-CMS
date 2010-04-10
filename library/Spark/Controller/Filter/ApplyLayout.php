<?php

class Spark_Controller_Filter_ApplyLayout 
  implements Spark_Controller_FilterInterface, Spark_UnifiedConstructorInterface
{
  
  protected $_layoutName = "default.phtml";
  protected $_layoutPath = null;
  
  protected $_layout = null;
  
  public function __construct($options = null)
  {
    if(!is_null($options)) {
      $this->setOptions($options);
    }
  }
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    
    $body = $response->getBody();
    
    $layout = $this->getLayout();
    
    $layout->content = $body;
    
    $response->setBody($layout->render($this->_layoutName));
    
  }
  
  public function setOptions($options)
  {
    Spark_Object_Options::setOptions($this, $options);
    return $this;
  }

  public function setLayoutName($layoutName)
  {
    $this->_layoutName = $layoutName;
    return $this;
  }

  public function setLayoutPath($layoutPath)
  {
    $this->_layoutPath = $layoutPath;
    $this->getLayout()->setScriptPath($layoutPath);
    return $this;
  }
  
  public function getLayout()
  {
    if(is_null($this->_layout)) {
      $this->_layout = new Zend_View;
      
      if(is_null($this->_layoutPath)) {
        throw new Spark_Controller_Exception("Please set the layout_path
          config directive for this filter");
      }
      
      $this->_layout->setScriptPath($this->_layoutPath);
    }
    
    return $this->_layout;
  }
  
}
