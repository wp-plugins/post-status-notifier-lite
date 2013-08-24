<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Psn_Patch_Database implements Ifw_Wp_Plugin_Update_Patch_Interface
{
    /**
     * @param Ifw_Util_Version $presentVersion
     * @param Ifw_Wp_Plugin_Manager $pm
     * @throws Ifw_Wp_Plugin_Update_Patch_Exception
     */
    public function execute(Ifw_Util_Version $presentVersion, Ifw_Wp_Plugin_Manager $pm)
    {
        // Updates for version 1.1
        // add bcc column to rules table
        if (!Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'bcc')) {

            $query = sprintf('ALTER TABLE `%s` ADD `bcc` TEXT NULL AFTER `cc`', Ifw_Wp_Proxy_Db::getTableName('psn_rules'));
            Ifw_Wp_Proxy_Db::getObject()->query($query);
        }

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Database';
    }

}
