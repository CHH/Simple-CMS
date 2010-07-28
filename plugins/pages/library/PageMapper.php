<?php

class PageMapper extends Spark_Model_Mapper_Abstract
{
  
  const DEFAULT_PAGES_PATH = "pages";
  
  protected $_entityClass   = "Page";
  protected $_pagePath;
  protected $_pageExtension;
  protected $_defaultPage;
  protected $_renderer;
  
  protected $_defaults = array(
    "page_path"      => self::DEFAULT_PAGES_PATH,
    "page_extension" => ".txt",
    "default_page"   => "index"
  );
  
  static protected $_defaultRenderer;
  
  public function init()
  {
    
  }
  
  public function setOptions(array $options)
  {
    $options = array_merge($this->_defaults, $options);
    return parent::setOptions($options);
  }
  
  public function find($id, $prefix = null)
  {
    $ds = DIRECTORY_SEPARATOR;
    
    $pagePath = $prefix . $ds . $id;
    
    if(file_exists(APPROOT . $ds . $this->_pagePath . $ds . $pagePath . $this->getPageExtension())) {
      $pagePath = $pagePath . $this->getPageExtension();
      
    } elseif(is_dir(APPROOT . $ds . $this->_pagePath . $ds . $pagePath)) {
      $pagePath = $pagePath . $ds . $this->_defaultPage . $this->getPageExtension();
      
    } else {
      return false;
    }
    
    $renderer = $this->getRenderer();
    
    $page = $this->create();
    
    $page->id       = $id;
    $page->prefix   = $prefix;
    $page->modified = filemtime(APPROOT . $ds . $this->_pagePath . $ds . $pagePath);
    
    $renderer->page = $page;
    
    try {
      Spark_Registry::get("EventDispatcher")->trigger(
        "pages.page_before_render",
        new PageEvent($page)
      );
      
      $page->content = $this->getRenderer()->render($pagePath);
      
      Spark_Registry::get("EventDispatcher")->trigger(
        "pages.page_after_render",
        new PageEvent($page)
      );
    } catch(Exception $e) {
      return false;
    }
    
    unset($renderer->id, $renderer->prefix, $renderer->modified);
    
    return $page;
  }
  
  public function findAll($prefix = null)
  {
    $path = APPROOT . DIRECTORY_SEPARATOR . $this->_pagePath
            . DIRECTORY_SEPARATOR . $prefix;
    
    $directory = new DirectoryIterator($path);
    $renderer = $this->getRenderer();
    
    $pages = new Spark_Model_Collection;
    
    foreach($directory as $entry) {
      if($entry->isFile() and !$entry->isDot()) {
        $page = $this->create();
        
        $page->id = str_replace($this->getPageExtension(), "", $entry->getFilename());
        $page->prefix = $prefix;
        $page->modified = $entry->getMTime();
        
        $renderer = $this->getRenderer();
        $renderer->page = $page;
        
        $page->content = $renderer->render($prefix . DIRECTORY_SEPARATOR . $entry->getFilename());
        
        unset($renderer->id, $renderer->prefix, $renderer->modified);
        
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
      $filename = APPROOT . DIRECTORY_SEPARATOR . $this->_pagePath . DIRECTORY_SEPARATOR . $id . $this->getPageExtension();
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
      if(is_null(self::$_defaultRenderer)) {
        $this->_renderer = new Zend_View;
      } else {
        $this->_renderer = self::$_defaultRenderer;
      }
      
      $this->_renderer->addScriptPath(APPROOT . DIRECTORY_SEPARATOR . $this->_pagePath);
      $this->_renderer->addHelperPath("Spark" . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . "Helper", "Spark_View_Helper");
      $this->_renderer->registerHelper(new View_Helper_Pages, "pages");
    }
    
    return $this->_renderer;
  }
  
  public function setRenderer(Zend_View_Interface $renderer)
  {
    $this->_renderer = $renderer;
    return $this;
  }
  
  static public function setDefaultRenderer(Zend_View_Interface $renderer)
  {
    self::$_defaultRenderer = $renderer;
  }
  
  static public function getDefaultRenderer()
  {
    return self::$_defaultRenderer;
  }
  
}
