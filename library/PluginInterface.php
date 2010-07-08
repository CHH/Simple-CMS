<?php

interface PluginInterface
{
  /**
   * bootstrap() - Gets called from the main bootstrap at plugin load time
   */
  public function bootstrap();
  
}
