<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Admin_Menu_Metabox_IfwFeed extends Ifw_Wp_Plugin_Admin_Menu_Metabox_RssFeed
{
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_RssFeed::_initFeedUrl()
     */
    protected function _initFeedUrl()
    {
        return 'http://www.ifeelweb.de/?feed=rss2';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'ifeelweb_de';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return 'www.ifeelweb.de Feed';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }
}
