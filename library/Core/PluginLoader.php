<?php

interface PluginLoader
{
  
  public function load($plugin);
  
  public function loadDirectory($pluginPath = null);
  
}
