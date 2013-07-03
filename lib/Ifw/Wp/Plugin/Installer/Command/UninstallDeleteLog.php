<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Tries to delete the log file if it exists in case in can not be deleted by WP uninstall process
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Installer_Command_UninstallDeleteLog implements Ifw_Wp_Plugin_Installer_UninstallInterface
{
    /**
     * @param Ifw_Wp_Plugin_Manager|null $pm
     * @return mixed|void
     */
    public static function execute($pm)
    {
        if (!($pm instanceof Ifw_Wp_Plugin_Manager)) {
            return;
        }

        $logFilePath = $pm->getPathinfo()->getRoot() . 'log/plugin.log';
        if (file_exists($logFilePath)) {
            unlink($logFilePath);
        }
    }
}
