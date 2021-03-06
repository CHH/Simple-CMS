<?php
/**
 * Plugin Abstract
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
namespace Core\Plugin;

/**
 * Plugin Abstract
 *
 * Provides a base class for plugins which provides access to the plugin's environment
 */
abstract class AbstractPlugin implements Plugin
{
    /**
     * Absolute path to the directory of the plugin
     * @var string
     */
    protected $path;

    protected $environment;

    /**
     * init() - Gets called by the main bootstrap when the plugin gets loaded
     */
    function init()
    {}

	/**
	 * Set the plugin's environment
	 *
	 * @param  Environment    $env
	 * @return AbstractPlugin
	 */
    function setEnvironment(Environment $env)
    {
        $this->environment = $env;
        return $this;
    }

    /**
     * Returns the absolute path to the plugin directory
     *
     * @return string
     */
    function getPath()
    {
        if (is_null($this->path)) {
            $this->path = PLUGINS . DIRECTORY_SEPARATOR . strtolower(get_class($this));
        }
        return $this->path;
    }

    /**
     * Sets the absolute path to the plugin directory
     *
     * @param  string
     * @return Plugin
     */
    function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Load an other Plugin
     *
     * @throws InvalidArgumentException
     *
     * @param  string $plugin,... Plugins to load
     * @return Plugin
     */ 
    protected function depend($on)
    {
        $plugins = func_get_args();
        $this->environment->depend($plugins);
    }

    /**
     * export() - Exports an object so other plugins can import it
     *
     * @param string $var
     * @param mixed $value
     * @return Plugin
     */
    protected function export($key, $object)
    {
        $this->environment->export($key, $object);
        return $this;
    }

    /**
     * import() - Returns an object from the PluginLoader
     *
     * @param string $var
     * @return mixed
     */
    protected function import($key)
    {
        return $this->environment->import($key);
    }
}
