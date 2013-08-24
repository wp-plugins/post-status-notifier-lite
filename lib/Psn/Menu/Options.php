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
class Psn_Menu_Options extends Ifw_Wp_Plugin_Menu_Page_Options
{
    public function onLoad()
    {
        $application = $this->_pm->getBootstrap()->getApplication();

        if ($application->getAdapter() instanceof Ifw_Wp_Plugin_Application_Adapter_ZendFw) {
            Ifw_Wp_Proxy_Action::add('load-'. $this->getPageHook(), array($application, 'render'));
        }
    }

    public function handle()
    {
        $application = $this->_pm->getBootstrap()->getApplication();

        if ($application->getAdapter() instanceof Ifw_Wp_Plugin_Application_Adapter_ZendFw) {
            $application->display();
        }
    }

}
