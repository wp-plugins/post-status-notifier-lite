<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Access 
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * Checks if it is an general WP admin access
     * @return bool
     */
    public function isAdmin()
    {
        if (function_exists('is_admin')) {
            return is_admin();
        }
        return false;
    }

    /**
     * Checks if it is an exact access to this plugin's admin pages
     * @return bool
     */
    public function isPlugin()
    {
        if (isset($_GET['page']) &&
            (strpos($_GET['page'], $this->_pm->getPathinfo()->getDirname()) !== false || strpos($_GET['page'], $this->_pm->getAbbrLower()) !== false) ||
            (Ifw_Wp_Ajax_Manager::isAccess() && isset($_REQUEST['action']) &&
                strpos($_REQUEST['action'], 'load-'. $this->_pm->getAbbrLower()) === 0)) {
            return true;
        }
        return false;
    }

    public function isAjax()
    {
        return Ifw_Wp_Ajax_Manager::isAccess();
    }

    public function isWidgetAdmin()
    {

    }

    /**
     * @param $page
     * @return bool
     */
    public function isPage($page)
    {
        return $_GET['page'] == $page;
    }

    /**
     * @return bool
     */
    public function getPage()
    {
        return isset($_GET['page']) ? $_GET['page'] : null;
    }
}
