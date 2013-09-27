<?php
/**
 * Overwrites Zend_Loader method loadClass
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Zend_Loader extends IfwZend_Loader
{
    /**
     * 
     * @param string $class
     * @param unknown_type $dirs
     * @throws IfwZend_Exception
     */
    public static function loadClass($class, $dirs = null)
    {
        if (Ifw_Wp_Autoloader::autoload($class)) {
            return;
        }
        
        parent::loadClass($class, $dirs);
    }
}