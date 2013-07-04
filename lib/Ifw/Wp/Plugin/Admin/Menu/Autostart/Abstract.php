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
abstract class Ifw_Wp_Plugin_Admin_Menu_Autostart_Abstract implements Ifw_Wp_Plugin_Admin_Menu_Autostart_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Admin_Menu
     */
    protected $_menu;


    /**
     * @param Ifw_Wp_Plugin_Admin_Menu $menu
     */
    public function __construct(Ifw_Wp_Plugin_Admin_Menu $menu)
    {
        $this->_menu = $menu;
    }

}
