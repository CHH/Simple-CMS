<?php

class Page extends Spark_Model_Entity
{
  const DEFAULT_PATH = "/pages";
  
  private static $_mapper;
  private static $_renderer;
  private static $_path = self::DEFAULT_PATH;
  
  private $_filename;
  
  protected $_data = array(
    "id"       => "",
    "content"  => "",
    "modified" => ""
  );
  
  public function __set($property, $value) {
    if($property == "modified" and is_int($value)) {
      $value = date("Y-m-d H:i:s", $value);
    }
    parent::__set($property, $value);
    
    Spark_Registry::get("EventDispatcher")->trigger("pages.page_modified", new PageEvent($this));
  }
  
  public function __get($property)
  {
    if($property == "content" and $this->_data['content'] == null) {
      $textile = new Spark_View_Helper_Textile;
      
      Spark_Registry::get("EventDispatcher")->trigger("pages.page_before_render",new PageEvent($this));

      self::getRenderer()->page = $this;
      $this->_data['content'] = $textile->parse(self::getRenderer()->render($this->_filename));
      
      Spark_Registry::get("EventDispatcher")->trigger("pages.page_after_render", new PageEvent($this));

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

  public static function find($page)
  {
    return self::_getMapper()->find(self::$_path . DIRECTORY_SEPARATOR . $page);
  }
  
  public static function findAll($inDirectory = null)
  {
    return self::_getMapper()->findAll(self::$_path . DIRECTORY_SEPARATOR . $inDirectory);
  }
  
  public function save()
  {
    self::_getMapper()->save($this);
    return $this;
  }

  public function delete()
  {
    self::_getMapper()->delete($this);
    return $this;
  }

  public function setFilename($filename)
  {
    $this->_filename = $filename;
  }

  public function getFilename()
  {
    return $this->_filename;
  }

  public static function setPath($path)
  {
    self::$_path = $path;
  }
  
  public static function getPath()
  {
    return self::$_path;
  }
  
  public static function setRenderer(Zend_View_Interface $renderer)
  {
    self::$_renderer = $renderer;
  }

  public static function getRenderer()
  {
    return self::$_renderer;
  } 
  
  private static function _getMapper()
  {
    if (null === self::$_mapper) {
      self::$_mapper = new PageMapper;
    }
    return self::$_mapper;
  }
}
