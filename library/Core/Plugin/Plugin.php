<?php

namespace Core\Plugin;

interface Plugin
{
    /**
     * Gets called from the plugin loader after instantiation
     */
    public function init();
}
