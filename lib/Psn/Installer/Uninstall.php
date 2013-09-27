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
    public static function execute($pm, $networkwide = false)
    {
        if (Ifw_Wp_Proxy_Blog::isMultisite() && $networkwide == true) {

            // multisite installation
            $currentBlogId = Ifw_Wp_Proxy_Blog::getBlogId();

            foreach (Ifw_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                Ifw_Wp_Proxy_Blog::switchToBlog($blogId);
                self::_dropTable();
            }
            Ifw_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // single blog installation
            self::_dropTable();
        }
    }

    protected static function _dropTable()
    {
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS `'. $wpdb->prefix .'psn_rules`');
    }
}
