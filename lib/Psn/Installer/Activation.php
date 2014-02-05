<?php
/**
 * Executes on plugin activation 
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) ifeelweb.de
 * @version     $Id$
 * @package     Psn_Installer
 */
class Psn_Installer_Activation implements Ifw_Wp_Plugin_Installer_ActivationInterface
{
    /**
     * @var Psn_Patch_Database
     */
    protected $_dbPatcher;



    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Installer_ActivationInterface::execute()
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm, $networkwide = false)
    {
        $this->_dbPatcher = new Psn_Patch_Database();

        if (Ifw_Wp_Proxy_Blog::isMultisite() && $networkwide == true) {

            // multisite installation
            $currentBlogId = Ifw_Wp_Proxy_Blog::getBlogId();

            foreach (Ifw_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                Ifw_Wp_Proxy_Blog::switchToBlog($blogId);
                $this->_createTable();
            }
            Ifw_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // single blog installation
            $this->_createTable();
        }

    }

    /**
     * Creates table and checks for new fields since version 1.0
     */
    protected function _createTable()
    {
        global $wpdb;

        $wpdb->query('
            CREATE TABLE IF NOT EXISTS `'. $wpdb->prefix .'psn_rules` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
              `posttype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `status_before` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `status_after` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              `notification_subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              `notification_body` text COLLATE utf8_unicode_ci NOT NULL,
              `recipient` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
              `to` varchar(255) COLLATE utf8_unicode_ci NULL,
              `cc` text COLLATE utf8_unicode_ci,
              `bcc` text COLLATE utf8_unicode_ci,
              `active` tinyint(1) NOT NULL DEFAULT "1",
              `service_email` tinyint(1) NOT NULL DEFAULT "0",
              `service_log` tinyint(1) NOT NULL DEFAULT "0",
              `categories` text COLLATE utf8_unicode_ci,
              `from` varchar(255) COLLATE utf8_unicode_ci NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="Plugin: Post Status Notifier";
        ');

        // if the table already existed (eg on update) this will check if all new fields are present
        $this->_dbPatcher->updateRulesTable();
    }
}
