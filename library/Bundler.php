<?php

class Bundler
{
  const GITHUB_BASE_URL = "http://www.github.com";
  const LOCATION_GITHUB = "github:";
  
  protected $_bundles = array();
  
  protected $_loadPath;
  
  public function __construct(array $bundles = array()) 
  {
    $this->_bundles = $bundles;
  }
  
  public function bundleAll(array $bundles = array())
  {
    if (!$bundles) {
      $bundles = $this->_bundles;
    }
    
    foreach ($bundles as $library => $location) {
      try {
        $this->bundle($library, $location);
        
      } catch (BundleException $e) {
        
      }
    }
    
    return $this;
  }
  
  public function bundle($library, $location)
  {
    if (strpos($location, self::LOCATION_GITHUB)) {
      $this->_loadArchiveFromGithub($location, $library);
    } else {
      $this->_loadArchiveFromUrl($location, $library);
    }
    
    return $this;
  }
  
  protected function _loadArchiveFromGithub($location, $namespace)
  {
    $file = str_replace("github:", GITHUB_BASE_URL, $location) . "/zipball/master";
    
    $zip = new ZipArchive;
    $state = $zip->open($file);
    
    $libraryIndex = $zip->locateName($namespace);
    
    $zip->extractTo($this->getLoadPath(), $libraryIndex);
  }
  
  protected function _loadArchiveFromUrl($url, $namespace)
  {
    
  }
  
  public function getLoadPath()
  {
    return $this->_loadPath;
  }
  
  public function setLoadPath($loadPath)
  {
    $this->_loadPath = $loadPath;
    return $this;
  }
  
  public function getBundles()
  {
    return $this->_bundles;
  }
  
  public function setBundles(array $bundles) 
  {
    $this->_bundles = $bundles;
    return $this;
  }
  
}
