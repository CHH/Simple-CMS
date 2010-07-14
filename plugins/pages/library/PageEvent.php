<?php

class PageEvent extends Spark_Event_Event
{
  protected $_page;  
  
  public function __construct(Page $page)
  {
    $this->setPage($page);
    parent::__construct();
  }
  
  public function setPage(Page $page)
  {
    $this->_page = $page;
    return $this;
  }
  
  public function getPage()
  {
    return $this->_page;
  }
  
}
