<?php
/**
 * Executes on plugin uninstall
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @version     $Id$
 * @package     Psn_Installer
 */
class Psn_Installer_Uninstall implements Ifw_Wp_Plugin_Installer_UninstallInterface
{
    /** (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Installer_UninstallInterface::execute()
     */
    public static function execute($pm)
    {
        global $wpdb, $table_prefix;
        $wpdb->query('DROP TABLE IF EXISTS `'. $table_prefix .'psn_rules`');
    }
}
