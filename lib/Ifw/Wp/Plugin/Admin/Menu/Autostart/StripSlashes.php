<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Strip slashes from $_POST values
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Plugin_Admin_Menu_Autostart_StripSlashes extends Ifw_Wp_Plugin_Admin_Menu_Autostart_Abstract
{
    public function execute()
    {
        Ifw_Wp_Proxy_Action::addWpLoaded(array($this, 'stripslashes'));
    }

    public function stripslashes()
    {
        $_POST = array_map('stripslashes_deep', $_POST);
    }

}
