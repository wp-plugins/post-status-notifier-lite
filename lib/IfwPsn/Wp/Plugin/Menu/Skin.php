<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Admin skin loader
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class IfwPsn_Wp_Plugin_Menu_Skin
{
    public static function loadSkin(IfwPsn_Wp_Plugin_Manager $pm)
    {
        if ($pm->getEnv()->hasSkin()) {
            IfwPsn_Wp_Proxy_Style::loadAdmin('admin-style', $pm->getEnv()->getSkinUrl() . 'style.css');
        }
    }
}
