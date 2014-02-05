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
abstract class Ifw_Wp_Plugin_Menu_Page_Sub implements Ifw_Wp_Plugin_Menu_Page_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Manager
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

    protected $_pageHook;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct($pm)
    {
        $this->_pm = $pm;
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
        if ($this->getCallback() instanceof Ifw_Wp_Plugin_Application_PageMapperInterface) {
            $this->getCallback()->handlePage($this);
        } elseif (is_callable($this->getCallback())) {
            call_user_func($this->getCallback());
        } else {
            Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'submenu_page_callback', $this);
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
     * @param mixed $pageHook
     */
    public function setPageHook($pageHook)
    {
        $this->_pageHook = $pageHook;
    }

    /**
     * @return mixed
     */
    public function getPageHook()
    {
        return $this->_pageHook;
    }

}
