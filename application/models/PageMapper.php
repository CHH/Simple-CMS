<?php

class PageMapper extends Spark_Model_Mapper_Abstract
{
  
  protected $_entityClass = "Page";
  
  protected $_pagePath = "pages";
  
  protected $_pageExtension = ".txt";
  
  public function init()
  {
    $pagesConfig = Spark_Registry::get("PagesConfig");
    
    if(isset($pagesConfig->pages->extension)) {
      $this->setPageExtension($pagesConfig->pages->extension);
    }
    
    if(isset($pagesConfig->pages->path)) {
      $this->setPagePath($pagesConfig->pages->path);
    }
  }
  
  public function find($id, $prefix = null)
  {
    $pagePath = WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath . DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR
            . $id . $this->getPageExtension();
    
    if(!file_exists($pagePath)) {
      return false;
    }
    
    $page = $this->create();
    $page->content = file_get_contents($pagePath);
    $page->id = $id;
    $page->prefix = $prefix;
    
    return $page;
  }
  
  public function findAll($prefix = null)
  {
    $path = WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath
            . DIRECTORY_SEPARATOR . $prefix;
    
    $directory = new DirectoryIterator($path);
    
    foreach($directory as $entry) {
      if($entry->isFile()) {
        $page = $this->create();
        $page->id = str_replace($this->getPageExtension(), "", $entry->getFilename());
        $page->prefix = $prefix;
        $page->content = file_get_contents($entry->getPathname());
        
        $pages[] = $page;
        unset($page);
      }
    }
    
    return $pages;
  }
  
  public function save($entity)
  {
    if(!($entity instanceof $this->_entityClass)) {
      throw new Spark_Model_Exception("This Mapper can only save {$this->_entityClass} objects");
    }
    
    if(!file_exists($this->_getPageFilename($entity))) {
    
      $this->_beforeSaveFilters->process($entity);
      $result = @file_put_contents($this->_getPageFilename($entity), $entity->content);
      $this->_afterSaveFilters->process($entity);
      
    } else {
    
      $this->_beforeUpdateFilters->process($entity);
      $result = file_put_contents($this->_getPageFilename($entity), $entity->content);
      $this->_afterUpdateFilters->process($entity);
    }

    
    if($result === false) {
      throw new Spark_Model_Exception("Saving failed due to a write error");
    }
    
    return $this;
  }
  
  public function delete($id)
  {
    if($id instanceof $this->_entityClass) {
      $filename = $this->_getPageFilename($id);
    } else {
      /* Assume page is in the pages root */
      $filename = WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath . DIRECTORY_SEPARATOR . $id . $this->getPageExtension();
    }
    
    unlink($filename);
    
    return $this;
  }
  
  public function setPagePath($path)
  {
    $this->_pagePath = $path;
    return $this;
  }
  
  public function setPageExtension($extension)
  {
    $this->_pageExtension = $extension;
    return $this;
  }
  
  public function getPageExtension()
  {
    $extension = $this->_pageExtension;
    
    if(strpos($extension, ".") === false) {
      $extension = "." . $extension;
    }
    
    return $extension;
  }
  
  protected function _getPageFilename(Page $page)
  {
    $filename = WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath . DIRECTORY_SEPARATOR . $page->prefix 
                . DIRECTORY_SEPARATOR . $page->id . $this->getPageExtension();
                
    return $filename;
  }
  
}
