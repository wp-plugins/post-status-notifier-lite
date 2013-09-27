<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Premium ad metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Metabox_PremiumAd extends Ifw_Wp_Plugin_Metabox_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'premium-ad';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Go Premium!', 'ifw');
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /**
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::render()
     */
    public function render()
    {
        $tpl = Ifw_Wp_Tpl::getInstance($this->_pm);
        $tpl->display('premium_ad.html.twig', array(
            'plugin_homepage' => $this->_pm->getEnv()->getHomepage(),
            'premium_url' => $this->_pm->getConfig()->plugin->premiumUrl,
        ));
    }
}
