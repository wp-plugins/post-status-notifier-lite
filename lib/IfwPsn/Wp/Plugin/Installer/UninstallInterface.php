<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp
 */
interface IfwPsn_Wp_Plugin_Installer_UninstallInterface
{
    /**
     * @param $pm null|IfwPsn_Wp_Plugin_Manager
     * @return mixed
     */
    public static function execute($pm);
}
