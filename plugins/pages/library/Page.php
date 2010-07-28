<?php

class Page extends Spark_Model_Entity
{
  
  protected $_data = array(
    "id" => "",
    "prefix" => "",
    "content" => "",
    "modified" => ""
  );
  
  public function __set($property, $value) {
    if($property == "modified" and is_int($value)) {
      $value = date("Y-m-d H:i:s", $value);
    }
    parent::__set($property, $value);
    
    Spark_Registry::get("EventDispatcher")->trigger(
      "pages.page_modified",
      new PageEvent($this)
    );
  }
  
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
  
  public function __toString()
  {
    return $this->toString();
  }
  
  public function toString()
  {
    return $this->content;
  }
  
  public function toJson()
  {
    $data = $this->_data;
    $data["content"] = $this->content;
    return json_encode($data);
  }
  
}
