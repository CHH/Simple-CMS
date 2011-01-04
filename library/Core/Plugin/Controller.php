<?php
/**
 * Base Class for a plugin's controllers
 * 
 * Provides access to the parent plugin and to the environment
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Core\Plugin;

class Controller extends \Spark\Controller\ActionController
{
    static protected $environment;
    protected $plugin;

    static function setEnvironment(Environment $env)
    {
        static::$environment = $env;
    }

    protected function getPlugin()
    {
        if (null === $this->plugin) {
            $pluginName = str_camelize($this->request->getMetadata("module"));
            $this->plugin = static::$environment->getPlugin($pluginName);
        }
        return $this->plugin;
    }

    protected function import($var)
    {
        return static::$environment->import($var);
    }
}
