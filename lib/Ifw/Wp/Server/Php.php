<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Server_Php 
{
    /**
     * @param $extension
     * @return bool
     */
    public static function isExtensionLoaded($extension)
    {
        return extension_loaded($extension);
    }

    /**
     * @return bool
     */
    public static function isPdoMysql()
    {
        return self::isExtensionLoaded('pdo_mysql');
    }

    /**
     * @param null $extension
     * @return string
     */
    public static function getVersion($extension = null)
    {
        return phpversion($extension);
    }

    /**
     * @return mixed
     */
    public static function getServerSoftware()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * @return mixed
     */
    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}
