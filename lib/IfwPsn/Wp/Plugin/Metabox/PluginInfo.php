<?php
/**
 * Plugin info metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
require_once dirname(__FILE__) . '/Ajax.php';

class IfwPsn_Wp_Plugin_Metabox_PluginInfo extends IfwPsn_Wp_Plugin_Metabox_Ajax
{
    /**
     * Stores info content blocks
     * @var array
     */
    protected $_infoBlocks = array();
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::init()
     */
    public function initAjaxResponse()
    {
        parent::initAjaxResponse();
        if ($this->_pm->hasPremium() && $this->_pm->isPremium()) {
            $this->_addPremiumBlock();
        }
        $this->_addConnectBlock();
        $this->_addHelpBlock();
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'plugin_info';
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Plugin Info', 'ifw');
    }
    
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initPriority()
     */
    protected function _initPriority()
    {
        return 'core';
    }

    /**
     * Adds a content block
     *
     * @param $id
     * @param string $label
     * @param string $content
     * @param string $iconClass
     */
    public function addBlock($id, $label, $content, $iconClass)
    {
        IfwPsn_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_plugininfo_before_'. $id, $this);

        $this->_infoBlocks[] = array(
            'id' => $id,
            'label' => $label,
            'content' => $content,
            'iconClass' => $iconClass        
        );

        IfwPsn_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_plugininfo_after_'. $id, $this);
    }

    protected function _addPremiumBlock()
    {
        $content = __('You are using the premium version of this plugin.', 'ifw');

        $content = strtr($content, array(
            'target="_blank"' => 'target="_blank" class="ifw-external-link"',
        ));

        $this->addBlock('premium',
            __('Premium', 'ifw'),
            '<br>' . $content,
            'premium');
    }

    protected function _addConnectBlock()
    {
        $homepage = $this->_pm->getEnv()->getHomepage();
        $premiumUrl = $this->_pm->getConfig()->plugin->premiumUrl;

        $content = '';
        $content .= sprintf(__('Visit the <a href="%s" target="_blank">plugin homepage</a>', 'ifw'), $homepage);

        if (!empty($premiumUrl) && $premiumUrl != $homepage) {
            $content .= '<br>' . sprintf(__('Visit the <a href="%s" target="_blank">premium homepage</a> for the latest news.', 'ifw'), $premiumUrl);
        }

        $content .= '<br><a href="https://twitter.com/ifeelwebde" target="_blank">@ifeelweb ' . __('on Twitter', 'ifw') . '</a>';

        $this->addBlock('connect',
            __('Connect', 'ifw'),
            '<br>' . $content,
            'connect');
    }

    protected function _addHelpBlock()
    {
        $content = '';

        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $content .= sprintf(__('Read the <a href="%s" target="_blank">plugin documentation</a>', 'ifw'), $this->_pm->getConfig()->plugin->docUrl) . '<br>';
        }
        if (!empty($this->_pm->getConfig()->plugin->faqUrl)) {
            $content .= sprintf(__('Check the <a href="%s" target="_blank">FAQ</a>', 'ifw'), $this->_pm->getConfig()->plugin->faqUrl) . '<br>';
        }

        $content = strtr($content, array(
            'target="_blank"' => 'target="_blank" class="ifw-external-link"',
        ));

        $this->addBlock('help',
            __('Need help?', 'ifw'),
            '<br>' . $content,
            'help');
    }

    /**
     * Renders all content blocks
     * @return IfwPsn_Wp_Ajax_Response
     */
    public function getAjaxResponse()
    {
        $this->initAjaxResponse();

        $tpl = IfwPsn_Wp_Tpl::getInstance($this->_pm);

        $html = '';
        foreach ($this->_infoBlocks as $block) {
            $params = array(
                'label' => $block['label'],
                'content' => $block['content'],
                'iconClass' => $block['iconClass'],
            );
            $html .= $tpl->render('metabox_plugininfo_block.html.twig', $params);
        }

        $html .= '<p class="ifw-made-with-heart">This plugin was made with <img src="'. $this->_pm->getEnv()->getSkinUrl().
            'icons/heart.png" /> by <a href="http://www.ifeelweb.de/" target="_blank">ifeelweb.de</a></p>';
        $success = true;

        return new IfwPsn_Wp_Ajax_Response($success, $html);
    }
}
