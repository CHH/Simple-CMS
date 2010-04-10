<?php

class Spark_FlashMessage
{
  
  const NOTICE = 1;
  const WARNING = 2;
  const ERROR = 0;
  
  protected $_message;
  protected $_type = self::NOTICE;
  
  public function __construct($message, $type = null)
  {
    $this->_message = $message;
    
    if(null !== $type) {
      $this->_type = $type;
    }
  }
  
  public function getMessage()
  {
    return $this->_message;
  }
  
  public function getType()
  {
    return $this->_type;
  }
  
  public function setMessage($message)
  {
    $this->_message = $message;
    return $this;
  }
  
  public function setType($type)
  {
    $this->_type = $type;
    return $this;
  } 
  
}

?>
