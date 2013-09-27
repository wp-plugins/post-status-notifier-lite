<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Zend_Application_DefaultController extends IfwZend_Controller_Action
{
    /**
     * Application config
     * @var array
     */
    protected $_config;

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * Navigation object
     * @var IfwZend_Navigation
     */
    protected $_navigation;

    /**
     * Unique page name
     * @var string
     */
    protected $_pageHook;

    /**
     * @var IfwZend_Controller_Action_Helper_Redirector
     */
    protected $_redirector;

    /**
     * @var IfwZend_Controller_Request_Abstract
     */
    protected $_request;



    /**
     * Initializes the controller
     * Will be called on bootstrap before admin-menu/admin-init/load-[page]
     * Use onAdminMenu/onAdminInit etc otherwise
     */
    public function init()
    {
        $this->_initSession();

        $this->_redirector = $this->_helper->getHelper('Redirector');

        $this->_request = $this->getRequest();

        // set config
        $this->_config = $this->getInvokeArg('bootstrap')->getOptions();

        $this->_pm = $this->_config['pluginmanager'];

        $this->view->pm = $this->_pm;

        $this->_helper->layout()->setLayout('layout');

        $this->_pageHook = 'page-'. $this->_pm->getPathinfo()->getDirname() . '-' . $this->getRequest()->getActionName();
        $this->view->pageHook = $this->_pageHook;

//        Ifw_Wp_Proxy_Action::addAdminInit(array($this, 'initNavigation'));

        $this->initNavigation();

        $this->view->isSupportedWpVersion = Ifw_Wp_Proxy_Blog::isMinimumVersion($this->_pm->getConfig()->plugin->wpMinVersion);
        $this->view->notSupportedWpVersionMessage = sprintf(__('This plugin requires WordPress version %s for full functionality. Your version is %s. <a href="%s">Please upgrade</a>.', 'ifw'),
            $this->_pm->getConfig()->plugin->wpMinVersion,
            Ifw_Wp_Proxy_Blog::getVersion(),
            'http://wordpress.org/download/'
        );
    }

    /**
     * Prepare the use of Zend_Session for Flash_Messenger
     */
    protected function _initSession()
    {
        if (session_id() != '') {
            // session already started, use the Zend_Session hack for use in WordPress context and set it to started
            IfwZend_Session::setStarted(true);
        }
    }

    /**
     * Inits admin navigation
     */
    public function initNavigation()
    {
        $this->_navigation = new IfwZend_Navigation();

        Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_before_admin_navigation', $this->_navigation);

        $this->_loadNavigationPages();

        Ifw_Wp_Proxy_Action::doAction($this->_pm->getAbbrLower() . '_after_admin_navigation', $this->_navigation);

        $this->view->navigation = $this->_navigation;
    }

    /**
     * To be overwritten by plugin
     */
    protected function _loadNavigationPages()
    {
    }

    /**
     * Redirects to controller/action
     *
     * @param string $controller
     * @param string|\unknown_type $action
     * @param string $page
     * @param array $extra
     * @return void
     */
    protected function _gotoRoute($controller, $action='index', $page=null, $extra = array())
    {
        if ($page == null) {
            $page = $this->_pm->getPathinfo()->getDirname();
        }

        $urlOptions = array_merge(array(
            $this->_pm->getConfig()->getControllerKey() => $controller,
            $this->_pm->getConfig()->getActionKey() => $action,
            'page' => $page
        ), $extra);

        $this->_redirector->gotoRoute($urlOptions, 'requestVars');
    }

    /**
     * @param $page
     * @param null $action
     * @param null $extra
     */
    protected function _gotoPage($page, $action = null, $extra = null)
    {
        $location = 'admin.php?page='. $page;

        if (!empty($action)) {
            $location .= '&'. $this->_pm->getConfig()->getActionKey() . '=' . $action;
        }
        if (!empty($extra)) {
            $location .= $extra;
        }

        header('Location: '. $location);
    }

    /**
     * @return IfwZend_Controller_Action_Helper_FlashMessenger
     */
    public function getMessenger()
    {
        return IfwZend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    }

    /**
     * called on bootstrapping
     */
    public function onBootstrap()
    {}

    /**
     * called on WP action admin-menu
     */
    public function onAdminMenu()
    {}

    /**
     * called on WP action admin-init
     */
    public function onAdminInit()
    {}

    /**
     * called on WP action current_screen
     */
    public function onCurrentScreen()
    {}

    /**
     * called on WP action load-[option_page_hook]
     */
    public function onLoad()
    {}
}