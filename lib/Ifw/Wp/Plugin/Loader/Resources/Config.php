<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Inits config
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_Resources_Config implements Ifw_Wp_Plugin_Loader_Resources_Interface
{
    public function load(Ifw_Wp_Plugin_Loader_ResourceStorage $resourceStorage)
    {
        if (!$resourceStorage->has('Ifw_Wp_Pathinfo_Plugin')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve pathinfo object');
        }

        $pluginPathinfo = $resourceStorage->get('Ifw_Wp_Pathinfo_Plugin');
        
        $config = Ifw_Wp_Plugin_Config::getInstance($pluginPathinfo);
        $resourceStorage->add($config);
    }
}