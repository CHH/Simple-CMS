<?php

namespace Core\Plugin;

class StandardLoader implements Loader
{
    protected $pluginPath;
    protected $exports    = array();
    protected $plugins    = array();

    const ERROR_LOADING_PLUGIN       = 510;
    const ERROR_BOOTSTRAPPING_PLUGIN = 511;

    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    function setOptions(array $options)
    {
        \Spark\Options::setOptions($this, $options);
        return $this;
    }

    function loadDirectory($pluginPath = null) 
    {
        if(is_null($pluginPath)) {
            $pluginPath = $this->getPluginPath();
        }

        $pluginIterator = new \DirectoryIterator($pluginPath);

        $failedPlugins = array();

        foreach($pluginIterator as $entry) { 
            if($entry->isDir() and !$entry->isDot()) {
                try {
                    $this->load($entry->getFilename());
                  
                } catch(PluginException $e) {
                    $failedPlugins[$e->getPluginName()] = $e;
                }
            }
        }

        if($failedPlugins) {
            $failedPluginList = join(array_keys($failedPlugins), ", ");

            $e = new Exception(
               "main", 
               "Following plugins could not be loaded: {$failedPluginList}. Make sure 
                  these plugins are correctly installed",
               self::ERROR_LOADING_PLUGIN,
               $failedPlugins
            );

           throw $e;
        }
    }

    function load($pluginName)
    { 
        $pluginRegistry = $this->getPluginRegistry();
		
        if($pluginRegistry->has($pluginName)) {
            return false;
        }

        $ds = DIRECTORY_SEPARATOR;
        $pluginBootstrapFile = $this->getPluginPath() . $ds . $pluginName . $ds . $pluginName . ".php";

        if(!include_once($pluginBootstrapFile)) {
            $pluginDirectory = $this->getPluginPath() . $ds . $pluginName;

            // Failed to load the file
        }

        $plugin = new $pluginName;

        if (!$plugin instanceof Plugin) {
            // Not implementing Plugin interface
        }

        /*
         * If Plugin extends the Abstract Plugin, then give it some more information
         */
        if ($plugin instanceof AbstractPlugin) {
            $plugin->setPath($this->getPluginPath() . $ds . $pluginName);
            $plugin->setPluginLoader($this);
        }

        try {
            $plugin->init();

        } catch(Exception $e) {
            // exception thrown by plugin while bootstrapping
        }

        $pluginRegistry->set($pluginName, $plugin);

        return $plugin;
    }

    function setPluginPath($pluginPath)
    {
        $this->pluginPath = $pluginPath;
        return $this;
    }

    function getPluginPath()
    {
        if(!is_null($this->pluginPath)) {
            return $this->pluginPath;
        }
        throw new \UnexpectedValueException("Please set the plugin path correctly before you attempt to load plugins");
    }

    function setPluginRegistry(Spark_Registry $registry)
    {
        $this->plugins = $registry;
        return $this;
    }

    function getPluginRegistry()
    {
        return $this->plugins;
    }

    function getExports()
    {
    	return $this->exports;
    }
}
