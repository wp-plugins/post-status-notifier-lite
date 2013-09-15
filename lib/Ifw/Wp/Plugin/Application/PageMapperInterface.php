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
interface Ifw_Wp_Plugin_Application_PageMapperInterface
{
    /**
     * Represents the callback function of add_options_page, add_menu_page and add_submenu_page
     *
     * @param Ifw_Wp_Plugin_Menu_Page_Interface $page
     * @return mixed
     */
    public function handlePage(Ifw_Wp_Plugin_Menu_Page_Interface $page);
}
