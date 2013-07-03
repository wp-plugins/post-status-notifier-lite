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
class Ifw_Wp_Plugin_Admin_Skin
{
    public static function loadSkin(Ifw_Wp_Plugin_Manager $pm)
    {
        if ($pm->getEnv()->hasSkin()) {
            Ifw_Wp_Proxy_Style::loadAdmin('admin-style', $pm->getEnv()->getSkinUrl() . 'style.css');
        }
    }
}
