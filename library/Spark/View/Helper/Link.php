<?php

class Spark_View_Helper_Link extends Spark_View_Helper_HtmlElement
{
  
  protected $_element = "a";
  
  public function link($innerHtml = null)
  {
    $instance = new self;
    
    if(null !== $innerHtml) {
      $instance->html($innerHtml);
    }
    
    return $instance;
  }
  
  public function to($url)
  {
    if(!is_string($url)) {
      throw new InvalidArgumentException("You must supply a string or key 
        value pairs as value for the href attribute");
    }
    
    $this->href = $url;
    
    return $this;
  }
  
}

?>
