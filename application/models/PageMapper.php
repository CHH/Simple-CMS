<?php

class PageMapper extends Spark_Model_Mapper_Abstract
{
  
  protected $_entityClass = "Page";

  const DEFAULT_PAGES_PATH = "pages";
  
  protected $_pagePath = self::DEFAULT_PAGES_PATH;

  protected $_pageExtension = ".txt";
  
  protected $_defaultPage = "index";
  
  protected $_renderer = null;
  
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
    $ds = DIRECTORY_SEPARATOR;
    
    $pagePath = $prefix . $ds . $id;
    
    if(file_exists(WEBROOT . $ds . $this->_pagePath . $ds . $pagePath . $this->getPageExtension())) {
      $pagePath = $pagePath . $this->getPageExtension();
      
    } elseif(is_dir(WEBROOT . $ds . $this->_pagePath . $ds . $pagePath)) {
      $pagePath = $pagePath . $ds . $this->_defaultPage . $this->getPageExtension();
      
    } else {
      return false;
    }
    
    $renderer = $this->getRenderer();
    
    $page = $this->create();
    
    $renderer->id = $page->id = $id;
    $renderer->prefix = $page->prefix = $prefix;
    $renderer->modified = $page->modified = filemtime(WEBROOT . $ds . $this->_pagePath . $ds . $pagePath);
    
    try {
      $page->content = $this->getRenderer()->render($pagePath);
    } catch(Exception $e) {
      return false;
    }
    
    return $page;
  }
  
  public function findAll($prefix = null)
  {
    $path = WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath
            . DIRECTORY_SEPARATOR . $prefix;
    
    $directory = new DirectoryIterator($path);
    $renderer = $this->getRenderer();
    
    $pages = new PageCollection;
    
    foreach($directory as $entry) {
      if($entry->isFile() and !$entry->isDot()) {
        $page = $this->create();
        
        $renderer->id = $page->id = str_replace($this->getPageExtension(), "", $entry->getFilename());
        $renderer->prefix = $page->prefix = $prefix;
        $renderer->modified = $page->modified = $entry->getMTime();
        
        $page->content = $this->getRenderer()->render($prefix . DIRECTORY_SEPARATOR . $entry->getFilename());
        
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
  
  public function setDefaultPage($page)
  {
    if($page instanceof $this->_entityClass) {
      $page = $page->id;
      
    } elseif(!is_string($page)) {
      throw new Spark_Model_Exception("Please supply for the default page either
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
    $filename = $page->prefix . DIRECTORY_SEPARATOR . $page->id . $this->getPageExtension();
                
    return $filename;
  }
  
  public function getRenderer()
  {
    if(is_null($this->_renderer)) {
      $this->_renderer = new Zend_View;
      $this->_renderer->setScriptPath(WEBROOT . DIRECTORY_SEPARATOR . $this->_pagePath);
      $this->_renderer->addHelperPath(SPARK_PATH . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", "Spark_View_Helper");
      $this->_renderer->registerHelper(new View_Helper_Pages, "pages");
    }
    return $this->_renderer;
  }
  
  public function setRenderer(Zend_View_Interface $renderer)
  {
    $this->_renderer = $renderer;
    return $this;
  }
  
}
