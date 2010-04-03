<?php

interface PluginLoaderInterface
{
  
  public function load($id);
  
  public function loadDirectory($pluginPath = null);
  
}