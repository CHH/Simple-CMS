<?php

class Page extends Spark_Model_Entity
{
  
  protected $_data = array(
    "id" => "",
    "prefix" => "",
    "content" => ""
  );
  
  public function __get($property)
  {
    if($property == "content") {
      $textile = new Spark_View_Helper_Textile;
      return $textile->parse($this->_data["content"]);
      
    } elseif($property == "rawContent") {
      return $this->_data["content"];
    }
    
    return parent::__get($property);
  }
  
}