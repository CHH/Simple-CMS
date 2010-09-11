<?php

interface PluginLoader
{
  
  public function load($id);
  
  public function loadDirectory($pluginPath = null);
  
}
