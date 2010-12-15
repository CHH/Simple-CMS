<?php

namespace Core\Plugin;

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
	 * preDispatch() - FrontController Callback, gets called before a plugin
	 * command gets executed
	 */
	function preDispatch($request, $response)
	{}

	/**
	 * postDispatch() - FrontController Callback, gets called after a command of 
	 * this plugin is executed and before the response is sent back to the client.
	 */
	function postDispatch($request, $response)
	{}
    
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
	protected function depend()
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
