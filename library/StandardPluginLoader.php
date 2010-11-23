<?php

class StandardPluginLoader implements PluginLoader, Spark_Configurable
{

    protected $pluginPath = null;
    protected $exports    = null;
    protected $plugins    = null;

    const ERROR_LOADING_PLUGIN       = 510;
    const ERROR_BOOTSTRAPPING_PLUGIN = 511;

    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options)
    {
        Spark_Options::setOptions($this, $options);
        return $this;
    }

    public function loadDirectory($pluginPath = null) 
    {
        if(is_null($pluginPath)) {
            $pluginPath = $this->getPluginPath();
        }

        $pluginIterator = new DirectoryIterator($pluginPath);

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

            $e = new PluginException(
               "main", 
               "Following plugins could not be loaded: {$failedPluginList}. Make sure 
                  these plugins are correctly installed",
               self::ERROR_LOADING_PLUGIN,
               $failedPlugins
            );

           throw $e;
        }
    }

    public function load($pluginName)
    { 
        $pluginRegistry = $this->getPluginRegistry();
		
        if($pluginRegistry->has($pluginName)) {
            return false;
        }

        $ds                  = DIRECTORY_SEPARATOR;
        $pluginBootstrapFile = $this->getPluginPath() . $ds . $pluginName . $ds . $pluginName . ".php";

        if(!include_once($pluginBootstrapFile)) {
            $pluginDirectory = $this->getPluginPath() . $ds . $pluginName;

            throw new PluginLoadException(
                $plugin, 
                "The Plugin was not found in \"{$pluginDirectory}\". Please make sure 
                  you have installed the Plugin \"{$pluginName}\".", 
                self::ERROR_LOADING_PLUGIN
            );
        }

        $plugin = new $pluginName;

        if (!$plugin instanceof Plugin) {
            throw new PluginLoadException(
                $pluginName, 
                "The Plugin \"{$pluginName}\" does not implement the PluginInterface.", 
                self::ERROR_LOADING_PLUGIN
            );
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
            throw new PluginBootstrapException(
                $pluginName, 
                "There was an failure during bootstrapping of " 
                . "the plugin \"{$pluginName}\" with the message {$e->getMessage()}",
                self::ERROR_BOOTSTRAPPING_PLUGIN
            );
        }

        $pluginRegistry->set($pluginName, $plugin);

        return $plugin;
    }

    public function setPluginPath($pluginPath)
    {
        $this->pluginPath = $pluginPath;
        return $this;
    }

    public function getPluginPath()
    {
        if(!is_null($this->pluginPath)) {
            return $this->pluginPath;
        }
        throw new PluginException("Please set the plugin path correctly before you attempt to load plugins");
    }

    public function setPluginRegistry(Spark_Registry $registry)
    {
        $this->plugins = $registry;
        return $this;
    }

    public function getPluginRegistry()
    {
        if(null === $this->plugins) {
            $this->plugins = new Spark_Registry();
        }
        return $this->plugins;
    }

    public function getExports()
    {
    	if (null === $this->exports) {
			$this->exports = new Spark_Registry();
    	}
    	return $this->exports;
    }
}
