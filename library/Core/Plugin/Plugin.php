<?php
/**
 * Plugin Interface
 * 
 * Minimal interface for plugins
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Core\Plugin;

interface Plugin
{
    /**
     * Gets called from the plugin loader after instantiation
     */
    public function init();
}
