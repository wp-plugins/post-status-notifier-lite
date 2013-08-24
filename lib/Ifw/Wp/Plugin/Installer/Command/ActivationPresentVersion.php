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
class Ifw_Wp_Plugin_Installer_Command_ActivationPresentVersion implements Ifw_Wp_Plugin_Installer_ActivationInterface
{
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm)
    {
        $pm->getBootstrap()->getUpdateManager()->refreshPresentVersion();
    }
}
