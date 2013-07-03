<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Inits logger
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Loader_Resources_Logger implements Ifw_Wp_Plugin_Loader_Resources_Interface
{
    public function load(Ifw_Wp_Plugin_Loader_ResourceStorage $resourceStorage)
    {
        if (!$resourceStorage->has('Ifw_Wp_Pathinfo_Plugin')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve pathinfo object');
        }
        if (!$resourceStorage->has('Ifw_Wp_Plugin_Config')) {
            throw new Ifw_Wp_Plugin_Loader_Exception('Could not retrieve config object');
        }

        $pluginPathinfo = $resourceStorage->get('Ifw_Wp_Pathinfo_Plugin');
        $config = $resourceStorage->get('Ifw_Wp_Plugin_Config');

        if ($config->log->file != '') {
            $logFile = $config->log->file;
        } else {
            $logFile = $pluginPathinfo->getRoot() . 'log'. DIRECTORY_SEPARATOR . 'plugin.log';
        }

        if (is_writable($logFile)) {
            // writable log file found
            $pm = $resourceStorage->get('Ifw_Wp_Plugin_Manager');
            $logger = Ifw_Wp_Plugin_Logger::factory($pm, new IfwZend_Log_Writer_Stream($logFile));
            $resourceStorage->add($logger);
        }
    }
}
