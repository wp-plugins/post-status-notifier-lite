<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
interface Ifw_Wp_Plugin_Installer_ActivationInterface
{
    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm);
}
