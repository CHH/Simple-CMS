<?php

class Spark_View_Helper_HtmlElement
{
  
  protected $_element;
  protected $_attributes;
  protected $_innerHtml;
  
  protected $_view;
  
  public function htmlElement($element)
  {
    $instance = new self;
    $instance->setElement($element);
    return $instance;
  }
  
  public function setElement($element)
  {
    $this->_element = $element;
    return $this;
  }
  
  public function close()
  {
    
    $closingTag = "</{$this->_element}>";
    
    return $closingTag;
  }
  
  public function html($html = null)
  {
    if(null === $html) {
      return $this->_innerHtml;
    }
    
    $this->_innerHtml = $html;
    
    return $this;
  }
  
  public function toString()
  {
    $attributes = "";
    
    if( null !== $this->_attributes) {
      foreach($this->_attributes as $attribute=>$values) {
        if(is_array($values)) {
          $values = join($values, ' ');
        }
        $attributes .= " ${attribute}=\"${values}\"";
      }
    }
    
    $openingTag = "<{$this->_element}${attributes}>";
    
    if(null !== $this->_innerHtml) {
      $html = $openingTag . $this->_innerHtml . $this->close();
    } else {
      $html = $openingTag;
    }
    
    return $html;

  }

  public function __toString()
  {
    return $this->toString();
  }
  
  public function __call($attribute, $value)
  {
    $this->__set($attribute, $value);
    
    return $this;
  }
  
  public function __get($attribute) 
  {
    $htmlAttribute = $this->_camelCaseToDash($attribute);
    
    if(isset($this->_attributes[$htmlAttribute])) {
      return $this->_attributes[$htmlAttribute];
    }
  }
  
  public function __set($attribute, $value) 
  {
    $htmlAttribute = $this->_camelCaseToDash($attribute);
    
    $this->_attributes[$htmlAttribute] = $value;
  }
  
  protected function _camelCaseToDash($string)
  {
    $inflector = new Zend_Filter_Inflector(':string');
    $inflector->setRules(array(':string'=>array('Word_CamelCaseToDash','StringToLower')));
    $inflectedString = $inflector->filter(array('string'=>$string));
    
    return $inflectedString;
  }
  
  public function setView(Zend_View_Abstract $view) {
    $this->_view = $view;
    return $this;
  }
  
  public function getView()
  {
    return $this->_view;
  }
  
}
