<?php

interface Plugin
{
  /**
   * Gets called from the plugin loader after instantiation
   */
  public function init();
}
