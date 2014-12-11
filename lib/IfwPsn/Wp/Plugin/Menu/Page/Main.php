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
require_once dirname(__FILE__) . '/Interface.php';

abstract class IfwPsn_Wp_Plugin_Menu_Page_Main implements IfwPsn_Wp_Plugin_Menu_Page_Interface
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * The title which will be shown in the menu
     * @var string
     */
    protected $_menuTitle;

    /**
     * The HTML page title
     * @var string
     */
    protected $_pageTitle;

    protected $_capability;

    protected $_slug;

    protected $_callback = '';

    protected $_iconUrl = '';

    protected $_position;

    protected $_pageHook;

    protected $_subPages = array();


    /**
     * @param IfwPsn_Wp_Plugin_Manager
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;

        $this->init();
    }

    public function init()
    {
        IfwPsn_Wp_Proxy_Action::addAdminMenu(array($this, '_load'));
    }

    /**
     * Loads the menu
     */
    public function _load()
    {
        IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'before_load_menu_page', $this);

        $this->_pageHook = add_menu_page(
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->getCapability(),
            $this->getSlug(),
            array($this, 'handle'),
            $this->getIconUrl(),
            $this->getPosition()
        );

        /**
         * @var IfwPsn_Wp_Plugin_Menu_Page_Sub $subPage
         */
        foreach($this->getSubPages() as $subPage) {

            IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'before_load_submenu_page', $subPage);

            if ($subPage->isHidden()) {

                global $_registered_pages;
                $hookname = get_plugin_page_hookname( plugin_basename($subPage->getSlug()), $subPage->getSlug());
                add_action( $hookname, array($subPage, 'handle') );
                $subPage->setPageHook($hookname);
                $_registered_pages[$hookname] = true;

            } else {

                $subPageHook = add_submenu_page(
                    $this->getSlug(),
                    $subPage->getPageTitle(),
                    $subPage->getMenuTitle(),
                    $subPage->getCapability(),
                    $subPage->getSlug(),
                    array($subPage, 'handle')
                );

                $subPage->setPageHook($subPageHook);
            }

            IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'after_load_submenu_page', $subPage);
            if ($this->_pm->getAccess()->getPage() == $subPage->getSlug()) {
                $subPage->onLoad();
            }
        }

        IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'after_load_menu_page', $this);
        if ($this->_pm->getAccess()->getPage() == $this->getSlug()) {
            $this->onLoad();
        }
    }

    /**
     * @param string $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * Decides which function to call
     */
    public function _callback()
    {
        if ($this->getCallback() instanceof IfwPsn_Wp_Plugin_Application_PageMapperInterface) {
            $this->getCallback()->handlePage($this);
        } elseif (is_callable($this->getCallback())) {
            call_user_func($this->getCallback());
        } else {
            IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'menu_page_callback', $this);
        }
    }

    /**
     * @param mixed $capability
     * @return $this
     */
    public function setCapability($capability)
    {
        $this->_capability = $capability;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCapability()
    {
        if (empty($this->_capability)) {
            // set default to an administrator capability
            $this->_capability = 'activate_plugins';
        }
        return $this->_capability;
    }

    /**
     * @param string $iconUrl
     * @return $this
     */
    public function setIconUrl($iconUrl)
    {
        $this->_iconUrl = $iconUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return $this->_iconUrl;
    }

    /**
     * @param string $menuTitle
     * @return $this
     */
    public function setMenuTitle($menuTitle)
    {
        $this->_menuTitle = $menuTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->_menuTitle;
    }

    /**
     * @param string $pageTitle
     * @return $this
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        if (empty($this->_pageTitle)) {
            return $this->getMenuTitle();
        }
        return $this->_pageTitle;
    }

    /**
     * @param mixed $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @param mixed $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->_slug;
    }

    /**
     * @return mixed
     */
    public function getPageHook()
    {
        return $this->_pageHook;
    }

    /**
     * @return array
     */
    public function getSubPages()
    {
        return $this->_subPages;
    }

    /**
     * @var IfwPsn_Wp_Plugin_Menu_Page_Sub $subPage
     * @return $this
     */
    public function registerSubPage(IfwPsn_Wp_Plugin_Menu_Page_Sub $subPage)
    {
        array_push($this->_subPages, $subPage);
        IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'after_register_submenu_page', $subPage);
        return $this;
    }


}
