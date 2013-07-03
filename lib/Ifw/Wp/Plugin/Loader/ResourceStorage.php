<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * A simple way to store objects by name as we can not use SplObjectStorage for PHP 5.2.x compatibility
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_ResourceStorage
{
    /**
     * @var array
     */
    protected $_storage = array();
    
    /**
     * Adds an object to the storage
     * 
     * @param object $resource
     */
    public function add($resource)
    {
        if (!is_object($resource)) {
            return;
        }
        $name = get_class($resource);
        if (!in_array($name, array_keys($this->_storage))) {
            $this->_storage[$name] = $resource;
        }
    }
    
    /**
     * 
     * @param string $resourceName
     * @return boolean
     */
    public function has($resourceName)
    {
        return in_array($resourceName, array_keys($this->_storage));
    }
    
    /**
     * 
     * @param string $resourceName
     * @return multitype:
     */
    public function get($resourceName)
    {
        if ($this->has($resourceName)) {
            return $this->_storage[$resourceName];
        }
    }
}