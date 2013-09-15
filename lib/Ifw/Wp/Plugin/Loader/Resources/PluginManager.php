<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Inits plugin manager object
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_Resources_PluginManager implements Ifw_Wp_Plugin_Loader_Resources_Interface
{
    public function load(Ifw_Wp_Plugin_Loader_ResourceStorage $resourceStorage)
    {
        if (!$resourceStorage->has('Ifw_Wp_Pathinfo_Plugin')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve pathinfo object');
        }

        $pluginPathinfo = $resourceStorage->get('Ifw_Wp_Pathinfo_Plugin');
        
        $pm = Ifw_Wp_Plugin_Manager::init($pluginPathinfo);
        $resourceStorage->add($pm);
    }
}
