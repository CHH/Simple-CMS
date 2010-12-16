<?php

namespace Core\Plugin;

class Environment
{
    protected $exports = array();
    protected $plugins = array();
    
    /** @var Loader */
    protected $loader;
    
    function registerPlugin($name, Plugin $plugin)
    {
        if (isset($this->plugins[$name])) {
            return $this;
        }
        $this->plugins[$name] = $plugin;
        return $this;
    }
    
    function isRegistered($name)
    {
        return isset($this->plugins[$name]);
    }
    
    function getPlugin($name)
    {
        if (!$this->isRegistered($name)) {
            throw new \UnexpectedValueException("Plugin {$name} is not registered");
        }
        return $this->plugins[$name];
    }
    
    function setLoader(Loader $loader)
    {
        $this->loader = $loader;
        return $this;
    }
    
    function depend($on)
    {
        if (is_array($on) and 1 === func_num_args()) {
            $plugins = $on;
        } else {
            $plugins = func_get_args();
        }
        $loader = $this->loader;

        if (!$plugins) {
            throw new \InvalidArgumentException("No plugin given");
        }

        foreach ($plugins as $plugin) {
            $loader->load($plugin);
        }

        return $this;
    }
    
    function export($export, $object = null)
    {
        if (is_array($export)) {
            foreach ($export as $key => $value) {
                $this->export($key, $value);
            }
            return $this;
        }
        if (isset($this->exports[$export])) {
            throw new \InvalidArgumentException(sprintf(
                "The key %s is already exported", $export
            ));
        }
        $this->exports[$export] = $object;
        return $this;
    }
    
    function import($exported)
    {
        if (!isset($this->exports[$exported])) {
            throw new \InvalidArgumentException("The export {$exported} is not set");
        }
        return $this->exports[$exported];
    }
}
