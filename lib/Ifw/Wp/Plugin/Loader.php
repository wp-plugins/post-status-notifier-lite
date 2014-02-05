<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Handles the initial loading procedure and return the loader object
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */ 
class Ifw_Wp_Plugin_Loader
{
    /**
     * @param $pathinfo
     * @param null $loader
     * @return \Ifw_Wp_Plugin_Loader_Default|null|object
     * @throws Ifw_Wp_Plugin_Loader_Exception
     */
    public static function load($pathinfo, $loader = null)
    {
        if (is_object($loader) && !is_a($loader, 'Ifw_Wp_Plugin_Loader_Abstract')) {
            require_once dirname(__FILE__) . '/Loader/Exception.php';
            throw new Ifw_Wp_Plugin_Loader_Exception('Invalid loader object provided. Loader must extend Ifw_Wp_Plugin_Loader_Abstract.');
        }

        try {

            if (empty($loader)) {
                if (!class_exists('Ifw_Wp_Plugin_Loader_Default')) {
                    require_once dirname(__FILE__) . '/Loader/Default.php';
                }
                $loader = new Ifw_Wp_Plugin_Loader_Default($pathinfo);
            }

            // load the plugin
            $loader->load();

            $pm = $loader->getPluginManager();

            if (!$loader->getEnv()->isCli()) {
                $pm->bootstrap();
            }

        } catch (Ifw_Wp_Plugin_Loader_Exception $e) {
            $error = 'Error while loading plugin: ' . $e->getMessage();
        } catch (Exception $e) {
            $error = 'General error: ' . $e->getMessage();
        }

        if (isset($error)) {
            self::_handleError($error, $loader);
        }

        return $loader;
    }

    /**
     * @param $error
     * @param Ifw_Wp_Plugin_Loader_Abstract $loader
     */
    protected static function _handleError($error, Ifw_Wp_Plugin_Loader_Abstract $loader)
    {
        try {
            $logger = $loader->getLogger();
            $logger->err($error);
        } catch (Exception $e) {
            trigger_error($error, E_USER_ERROR);
        }
    }
}
