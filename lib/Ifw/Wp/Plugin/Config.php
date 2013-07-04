<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Plugin config based in Zend_Config_Ini
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */
class Ifw_Wp_Plugin_Config extends IfwZend_Config_Ini
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();

    /**
     * Retrieves singleton Ifw_Wp_Plugin_Config object
     *
     * @param \Ifw_Wp_Pathinfo_Plugin|\Ifw_Wp_Plugin_Pathinfo $pluginPathinfo
     * @internal param $string
     * @return Ifw_Wp_Plugin_Config
     */
    public static function getInstance(Ifw_Wp_Pathinfo_Plugin $pluginPathinfo)
    {
        $instanceToken = $pluginPathinfo->getDirname();
        
        if (!isset(self::$_instances[$instanceToken])) {
            $iniPath = $pluginPathinfo->getDirnamePath() . 'config.ini';
            $env = getenv('IFW_WP_ENV') ? getenv('IFW_WP_ENV') : 'production';
            self::$_instances[$instanceToken] = new self($iniPath, $env);
        }
        return self::$_instances[$instanceToken];
    }
}
