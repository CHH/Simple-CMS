<?php

class PageMapper implements Spark_Configurable
{ 
  protected $_entityClass = "Page";
  protected $_pageExtension;
  protected $_defaultPage;
  
  protected $_defaults = array(
    "page_extension" => ".txt",
    "default_page"   => "index"
  );

  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }
  
  public function setOptions(array $options)
  {
    $options = array_merge($this->_defaults, $options);
    Spark_Options::setOptions($this, $options);
  }
  
  public function find($name)
  {
    $ds = DIRECTORY_SEPARATOR;
    
    if(file_exists(APPROOT . $ds . $name . $this->getPageExtension())) {
      $pagePath = $ds . $name . $this->getPageExtension();
      
    } elseif(is_dir(APPROOT . $ds . $name)) {
      $pagePath = $ds . $name . $ds . $this->_defaultPage . $this->getPageExtension();
      
    } else {
      return false;
    }

    $folder = substr($pagePath, 0, strrpos($pagePath, $ds));
    
    $page = new Page;
    
    $page->name     = $name;
    $page->modified = filemtime(APPROOT . $ds . $name . $this->getPageExtension());
    $page->folder   = $folder;
    $page->setFilename($pagePath);
    
    return $page;
  }
  
  public function findAll($inDirectory = null)
  {
    $path = APPROOT . DIRECTORY_SEPARATOR . $inDirectory;
    
    $directory = new DirectoryIterator($path);
    $pages     = new Spark_Model_Collection;
    
    foreach($directory as $entry) {
      if(!$entry->isFile() or $entry->isDot() or pathinfo($entry->getFilename(), PATHINFO_EXTENSION) !== "txt") {
        continue;
      }
      
      $page           = new Page;
      $page->name     = str_replace($this->getPageExtension(), "", $entry->getFilename());
      $page->modified = $entry->getMTime();
      $page->folder   = DIRECTORY_SEPARATOR . $inDirectory;

      $page->setFilename($entry->getFilename());
      
      $pages[] = $page;
      unset($page);
    }
    
    return $pages;
  }
  
  public function save($entity)
  {
    if(!($entity instanceof $this->_entityClass)) {
      throw new Spark_Model_Exception("This Mapper can only save {$this->_entityClass} objects");
    }
    
    if(!file_exists($this->_getPageFilename($entity))) {
      $result = @file_put_contents($this->_getPageFilename($entity), $entity->rawContent);
      
    } else {
      $result = file_put_contents($this->_getPageFilename($entity), $entity->rawContent);
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
      $filename = APPROOT . DIRECTORY_SEPARATOR . $this->_pagePath . DIRECTORY_SEPARATOR . $id . $this->getPageExtension();
    }
    
    unlink($filename);
    
    return $this;
  }
  
  public function setDefaultPage($page)
  {
    if($page instanceof $this->_entityClass) {
      $page = $page->name;
      
    } elseif(!is_string($page)) {
      throw new InvalidArgumentException("Please supply for the default page either
        the name as string or a valid Page object");
    }
    
    $this->_defaultPage = $page;
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
    $filename = $page->prefix . DIRECTORY_SEPARATOR . $page->name . $this->getPageExtension();
                
    return $filename;
  }
  
}
