<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Tries to reset the options set by the plugin
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Installer_Command_UninstallResetOptions implements Ifw_Wp_Plugin_Installer_UninstallInterface
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

        $pm->getOptions()->reset();
    }
}