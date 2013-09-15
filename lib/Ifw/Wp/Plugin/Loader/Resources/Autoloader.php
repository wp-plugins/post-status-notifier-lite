<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Inits autoloading
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_Resources_Autoloader implements Ifw_Wp_Plugin_Loader_Resources_Interface
{
    public function load(Ifw_Wp_Plugin_Loader_ResourceStorage $resourceStorage)
    {
        if (!$resourceStorage->has('Ifw_Wp_Pathinfo_Plugin')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve pathinfo object');
        }
        
        $pluginPathinfo = $resourceStorage->get('Ifw_Wp_Pathinfo_Plugin');

        if (!class_exists('Ifw_Wp_Autoloader')) {
            require_once $pluginPathinfo->getRootLib() . 'Ifw/Wp/Autoloader.php';
        }
        Ifw_Wp_Autoloader::init($pluginPathinfo->getRootLib());
        Ifw_Wp_Autoloader::init($pluginPathinfo->getRootAdminMenu());
    }
}