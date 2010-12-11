<?php

namespace Core\Plugin;

class StandardLoader implements Loader
{
    protected $path;
    protected $exports    = array();
    protected $registered = array();

	protected $namespace = "Plugin";
	
    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    function setOptions(array $options)
    {
        \Spark\Options::setOptions($this, $options);
        return $this;
    }

    function loadAll() 
    {
        $pluginPath = $this->getPath();
		
        $pluginIterator = new \DirectoryIterator($pluginPath);

        $failedPlugins = array();

        foreach($pluginIterator as $entry) { 
            if($entry->isDir() and !$entry->isDot()) {
                try {
                	$this->load($entry->getFilename());
                } catch (\Exception $e) {
					// Log the error and continue loading
                }
            }
        }
        return $this;
    }

    function load($pluginName)
    { 
        $registered = $this->getRegistered();
        
        if ($this->isRegistered($pluginName)) return $this->registered[$pluginName];
        
        $ds = DIRECTORY_SEPARATOR;
        $pluginBootstrapFile = $this->getPath() . $ds . $pluginName . $ds . $pluginName . ".php";
		
        if(!include_once($pluginBootstrapFile)) {
            throw new Exception(sprintf(
				"The plugin %s was not found in %s, please make sure its correctly installed",
				$pluginName,
				$this->getPath() . $ds . $pluginName
            ));
        }

		$className = "\\" . $this->namespace . "\\" . $pluginName;
		
        $plugin = new $className;

        if (!$plugin instanceof Plugin) {
            throw new Exception("Plugins must implement the Core\Plugin\Plugin Interface");
        }

        /*
         * If Plugin extends the Abstract Plugin, then give it some more information
         */
        if ($plugin instanceof AbstractPlugin) {
            $plugin->setPath($this->getPath() . $ds . $pluginName);
            $plugin->setPluginLoader($this);
        }

        try {
            $plugin->init();

        } catch(\Exception $e) {
            throw new Exception(sprintf(
				"There was an exception while bootstrapping the plugin %s, with message %s",
				$pluginName,
				$e->getMessage()
            ), null, $e);
        }
		
        $this->register($pluginName, $plugin);
        return $plugin;
    }

    function setPath($pluginPath)
    {
        $this->path = $pluginPath;
        return $this;
    }

    function getPath()
    {
        if(!is_null($this->path)) {
            return $this->path;
        }
        throw new \UnexpectedValueException("Please set the plugin path correctly before you attempt to load plugins");
    }

	function isRegistered($plugin)
	{
		return isset($this->registered[$plugin]);
	}

    function getRegistered()
    {
        return $this->registered;
    }

    function getExports()
    {
    	return $this->exports;
    }

    protected function register($plugin, $instance)
	{
		if ($this->isRegistered($plugin)) return;

		$this->registered[$plugin] = $instance;
		return $this;
	}
}
