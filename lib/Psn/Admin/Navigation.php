<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Navigation
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var IfwZend_Navigation
     */
    protected $_navigation;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    public function load()
    {
        $this->_navigation = new IfwZend_Navigation();

        Ifw_Zend_Controller_Front::getInstance()->initRouter($this->_pm);

        $page = new Ifw_Zend_Navigation_Page_WpMvc(array(
            'label' => __('Overview', 'psn'),
            'page' => $this->_pm->getPathinfo()->getDirname(),
            'action' => 'index',
            'controller' => 'index',
            'route' => 'requestVars'
        ));
        $this->_navigation->addPage($page);

        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'after_admin_navigation_overview', $this->_navigation);

        $page = new Ifw_Zend_Navigation_Page_WpMvc(array(
            'label' => __('Rules', 'psn'),
            'page' => $this->_pm->getPathinfo()->getDirname(),
            'action' => 'index',
            'controller' => 'rules',
            'route' => 'requestVars'
        ));
        $this->_navigation->addPage($page);

        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'after_admin_navigation_rules', $this->_navigation);

        $page = new Ifw_Zend_Navigation_Page_WpMvc(array(
            'label' => __('Options', 'psn'),
            'controller' => 'options',
            'action' => 'index',
            'page' => $this->_pm->getPathinfo()->getDirname(),
            'route' => 'requestVars'
        ));
        $this->_navigation->addPage($page);

        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'after_admin_navigation_options', $this->_navigation);
    }

    /**
     * @return IfwZend_Navigation
     */
    public function getNavigation()
    {
        if (empty($this->_navigation)) {
            $this->load();
        }

        return $this->_navigation;
    }

    /**
     * @return array
     */
    public function getPagesWithHrefAndLabel()
    {
        $result = array();
        $nav = $this->getNavigation();

        /**
         * @var Ifw_Zend_Navigation_Page_WpMvc $page
         */
        foreach ($nav->getPages() as $page) {
            $result[] = array(
                'href' => Ifw_Wp_Proxy_Admin::getMenuUrl(
                    $this->_pm, $page->getController(),
                    $page->getAction(),
                    null,
                    array('module' => $page->getModule())),
                'label' => $page->getLabel()
            );
        }

        return $result;
    }
}
