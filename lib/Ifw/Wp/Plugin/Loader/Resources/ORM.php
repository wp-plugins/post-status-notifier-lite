<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Inits ORM if defined in config
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_Resources_ORM implements Ifw_Wp_Plugin_Loader_Resources_Interface
{
    public function load(Ifw_Wp_Plugin_Loader_ResourceStorage $resourceStorage)
    {
        if (!$resourceStorage->has('Ifw_Wp_Plugin_Config')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve config object');
        }

        $config = $resourceStorage->get('Ifw_Wp_Plugin_Config');

        if (isset($config->orm->init) && $config->orm->init == true) {
            Ifw_Wp_ORM::init($config->orm);
        }
    }
}
