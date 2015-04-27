<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Deactivation interface
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
interface IfwPsn_Wp_Plugin_Installer_DeactivationInterface
{
    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param bool $networkwide
     * @return mixed
     */
    public function execute(IfwPsn_Wp_Plugin_Manager $pm, $networkwide = false);
}
