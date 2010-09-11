<?php

interface Plugin
{
  /**
   * bootstrap() - Gets called from the main bootstrap at plugin load time
   */
  public function init();
  
}
