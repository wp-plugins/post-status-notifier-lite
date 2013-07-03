<?php
/**
 * Plugin info metabox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Wp_Plugin_Admin_Menu_Metabox_PluginInfo extends Ifw_Wp_Plugin_Admin_Menu_Metabox_Ajax
{
    /**
     * Stores info content blocks
     * @var array
     */
    protected $_infoBlocks = array();
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::init()
     */
    public function initAjaxResponse()
    {
        parent::initAjaxResponse();
        if ($this->_pm->hasPremium() && $this->_pm->isPremium()) {
            $this->_addPremiumBlock();
        }
        if ($this->_pm->isPremium()) {
            $this->_addVersionBlock();
        }
        $this->_addHelpBlock();
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initId()
     */
    protected function _initId()
    {
        return 'plugin_info';
    }
    
    /**
     * (non-PHPdoc)
     * @see Ifw_Wp_Plugin_Admin_Menu_Metabox_Abstract::_initTitle()
     */
    protected function _initTitle()
    {
        return __('Plugin info', 'ifw');
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
     * Adds a content block
     *
     * @param $id
     * @param string $label
     * @param string $content
     * @param string $iconClass
     */
    public function addBlock($id, $label, $content, $iconClass)
    {
        Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_plugininfo_before_'. $id, $this);

        $this->_infoBlocks[] = array(
            'id' => $id,
            'label' => $label,
            'content' => $content,
            'iconClass' => $iconClass        
        );

        Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_plugininfo_after_'. $id, $this);
    }
    
    /**
     * Adds version content block
     */
    protected function _addVersionBlock()
    {
        $installedVersion = $this->_pm->getEnv()->getVersion();

        $label = __('Version');
        $content = $installedVersion . '<br />';

        try {
            // fetch remote plugin info
            $pluginRemoteInfo = Ifw_Wp_Plugin_RemoteInfo::getInstance($this->_pm->getConfig()->plugin->uniqueId);

            // compare installed version to remote current version
            if (version_compare($installedVersion, $pluginRemoteInfo->getVersion()) >= 0) {
                $iconClass = 'ok';
                $content .= __('The plugin is up to date.', 'ifw');
            } else {
                $iconClass = 'error';
                $content .= __('There is a new version available.', 'ifw') .
                    ' <a href="'. $pluginRemoteInfo->getUpdateUrl() . '" target="_blank">'. __('Download') . '</a>';
            }
        } catch (Exception $e) {
            // error on fetching remote plugin info
            $iconClass = 'error';
            $content .= sprintf(__('Error while loading: %s'), $e->getMessage());
        }
        
        $this->addBlock('version', $label, $content, $iconClass);
    }

    protected function _addPremiumBlock()
    {
        $content = __('You are using the Premium version of this plugin.', 'ifw');

        if (!empty($this->_pm->getConfig()->plugin->premiumUrl)) {
            $content .= '<br>' . sprintf(__('Visit the <a href="%s" target="_blank">Premium homepage</a> for the latest news.', 'ifw'), $this->_pm->getConfig()->plugin->premiumUrl);
        }

        $this->addBlock('premium',
            __('Premium', 'ifw'),
            $content,
            'premium');
    }

    protected function _addHelpBlock()
    {
        $content = '';

        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $content .= sprintf(__('Read the <a href="%s" target="_blank">plugin documentation</a>', 'ifw'), $this->_pm->getConfig()->plugin->docUrl) . '<br>';
        }
        $content .= sprintf(__('Visit the <a href="%s" target="_blank">plugin homepage</a>', 'ifw'), $this->_pm->getEnv()->getHomepage());

        $this->addBlock('help',
            __('Need help?', 'ifw'),
            '<br>' . $content,
            'help');
    }

    /**
     * Renders all content blocks
     * @return Ifw_Wp_Ajax_Response
     */
    public function getAjaxResponse()
    {
        $this->initAjaxResponse();

        $tpl = Ifw_Wp_Tpl::getInstance($this->_pm);

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

        return new Ifw_Wp_Ajax_Response($success, $html);
    }
}
