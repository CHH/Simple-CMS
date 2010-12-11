<?php

namespace Core\Plugin;

interface Loader
{
  public function load($plugin);
  public function loadAll();
}
