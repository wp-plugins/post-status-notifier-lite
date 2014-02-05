<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
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
        $this->updateRulesTable();
    }

    /**
     * Updates the rule table, checks for missing fields after version 1.0
     */
    public function updateRulesTable()
    {
        // Updates for version 1.1
        // add bcc column to rules table
        if (!$this->isFieldBcc()) {
            $this->createRulesFieldBcc();
        }

        // Updates for version 1.3
        // add categories column to rules table
        if (!$this->isFieldCategories()) {
            $this->createRulesFieldCategories();
        }

        // Updates for version 1.4
        // add 'to', 'from' column to rules table
        if (!$this->isFieldTo()) {
            $this->createRulesFieldTo();
        }
        if (!$this->isFieldFrom()) {
            $this->createRulesFieldFrom();
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $ruleFields = array (
            'id',
            'name',
            'posttype',
            'status_before',
            'status_after',
            'notification_subject',
            'notification_body',
            'recipient',
            'cc',
            'bcc',
            'active',
            'service_email',
            'service_log',
            'categories',
        );

        $diff = array_diff(
            Ifw_Wp_Proxy_Filter::apply('psn_db_patcher_rule_fields', $ruleFields),
            Ifw_Wp_Proxy_Db::getTableFieldNames('psn_rules')
        );

        return empty($diff);
    }

    /**
     * @return bool
     */
    public function isFieldBcc()
    {
        return Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'bcc');
    }

    /**
     * @return bool
     */
    public function isFieldCategories()
    {
        return Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'categories');
    }

    /**
     * @return bool
     */
    public function isFieldTo()
    {
        return Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'to');
    }

    /**
     * @return bool
     */
    public function isFieldFrom()
    {
        return Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'from');
    }

    /**
     * Create field "bcc" on psn_rules table
     * @since 1.1
     */
    public function createRulesFieldBcc()
    {
        $query = sprintf('ALTER TABLE `%s` ADD `bcc` TEXT NULL AFTER `cc`', Ifw_Wp_Proxy_Db::getTableName('psn_rules'));
        Ifw_Wp_Proxy_Db::getObject()->query($query);
    }

    /**
     * Create field "categories" on psn_rules table
     * @since 1.3
     */
    public function createRulesFieldCategories()
    {
        $query = sprintf('ALTER TABLE `%s` ADD `categories` TEXT NULL', Ifw_Wp_Proxy_Db::getTableName('psn_rules'));
        Ifw_Wp_Proxy_Db::getObject()->query($query);
    }

    /**
     * Create field "to" on psn_rules table
     * @since 1.4
     */
    public function createRulesFieldTo()
    {
        // ALTER TABLE  `wp_psn_rules` ADD  `to` VARCHAR( 255 ) NULL AFTER  `recipient`
        $query = sprintf('ALTER TABLE `%s` ADD `to` VARCHAR( 255 ) NULL AFTER  `recipient`', Ifw_Wp_Proxy_Db::getTableName('psn_rules'));
        Ifw_Wp_Proxy_Db::getObject()->query($query);
    }

    /**
     * Create field "from" on psn_rules table
     * @since 1.4
     */
    public function createRulesFieldFrom()
    {
        // ALTER TABLE  `wp_psn_rules` ADD  `from` VARCHAR( 255 ) NULL AFTER  `recipient`
        $query = sprintf('ALTER TABLE `%s` ADD `from` VARCHAR( 255 ) NULL ', Ifw_Wp_Proxy_Db::getTableName('psn_rules'));
        Ifw_Wp_Proxy_Db::getObject()->query($query);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Database';
    }
}
