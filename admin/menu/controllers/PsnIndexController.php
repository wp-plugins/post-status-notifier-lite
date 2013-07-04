<?php
/**
 * Index controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class PsnIndexController extends PsnApplicationController
{
    /**
     * (non-PHPdoc)
     * @see PsnApplicationController::init()
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    /**
     * (non-PHPdoc)
     * @see IfwZend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        if ($this->getRequest()->getActionName() == 'index') {
            $this->enqueueScripts();
        }
    }

    public function onCurrentScreen()
    {
        if ($this->_request->getActionName() == 'index') {

            $pointer = new Ifw_Wp_Plugin_Admin_Menu_Pointer('psn_link_create_rule');
            $pointer->setHeader(__('Manage rules', 'psn'))
                ->setContent(sprintf(__('In the "Rules" section you can manage your post status notification rules.<br>Just try it and <a href="%s">create a new rule</a>.', 'psn'), $this->_admin->getMenu()->getUrl('rules', 'create')))
                ->setEdge('top')->setAlign('left')
                ->renderTo('nav-rules');
        }
    }

    /**
     * 
     */
    public function indexAction()
    {
        // set up contextual help
        $this->_menu->addHelp($this->_getHelpText(), __('Overview', 'psn'), $this->_getHelpSidebar());
        
        // set up metaboxes
        $metaBoxContainerLeft = new Ifw_Wp_Plugin_Admin_Menu_Metabox_Container($this->_pageHook, 'left');
        Ifw_Wp_Proxy_Action::doAction('psn_admin_overview_before_metabox_left', $metaBoxContainerLeft);
        $metaBoxContainerLeft->addMetabox(new Psn_Admin_Metabox_Rules($this->_pm));
        Ifw_Wp_Proxy_Action::doAction('psn_admin_overview_after_metabox_left', $metaBoxContainerLeft);
        
        $metaBoxContainerRight = new Ifw_Wp_Plugin_Admin_Menu_Metabox_Container($this->_pageHook, 'right');
        Ifw_Wp_Proxy_Action::doAction('psn_admin_overview_before_metabox_right', $metaBoxContainerRight);
        if ($this->_pm->hasPremium() && $this->_pm->isPremium() == false) {
            $metaBoxContainerRight->addMetabox(new Ifw_Wp_Plugin_Admin_Menu_Metabox_PremiumAd($this->_pm));
        }
        $metaBoxContainerRight->addMetabox(new Ifw_Wp_Plugin_Admin_Menu_Metabox_PluginInfo($this->_pm));
        $metaBoxContainerRight->addMetabox(new Ifw_Wp_Plugin_Admin_Menu_Metabox_PluginStatus($this->_pm));
        $metaBoxContainerRight->addMetabox(new Ifw_Wp_Plugin_Admin_Menu_Metabox_IfwFeed($this->_pm));
        Ifw_Wp_Proxy_Action::doAction('psn_admin_overview_after_metabox_right', $metaBoxContainerRight);
        
        $this->view->metaBoxContainerLeft = $metaBoxContainerLeft;
        $this->view->metaBoxContainerRight = $metaBoxContainerRight;

    }
    
    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return __('This is an overview of your plugin settings', 'psn');
    }
    
    /**
     *
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>', 
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
                $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }

    public function enqueueScripts()
    {
        Ifw_Wp_Proxy_Script::loadAdmin('jquery-ui-dialog');
        Ifw_Wp_Proxy_Style::loadAdmin('wp-jquery-ui');
        Ifw_Wp_Proxy_Style::loadAdmin('wp-jquery-ui-dialog');
    }
}
