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
 * @package   IfwPsn_Wp_Plugin
 */ 
class IfwPsn_Wp_Plugin_Loader
{
    /**
     * @param $pathinfo
     * @param null $loader
     * @return \IfwPsn_Wp_Plugin_Loader_Default|null|object
     * @throws IfwPsn_Wp_Plugin_Loader_Exception
     */
    public static function load($pathinfo, $loader = null)
    {
        if (is_object($loader) && !($loader instanceof IfwPsn_Wp_Plugin_Loader_Abstract)) {
            require_once dirname(__FILE__) . '/Loader/Exception.php';
            throw new IfwPsn_Wp_Plugin_Loader_Exception('Invalid loader object provided. Loader must extend IfwPsn_Wp_Plugin_Loader_Abstract.');
        }

        try {

            require_once dirname(__FILE__) . '/../HelperFunctions.php';

            if (empty($loader)) {
                require_once dirname(__FILE__) . '/Loader/Default.php';
                $loader = new IfwPsn_Wp_Plugin_Loader_Default($pathinfo);
            }

            // load the plugin
            $loader->load();

            $pm = $loader->getPluginManager();

            if (!$loader->getEnv()->isCli()) {
                $pm->getLogger()->logPrefixed('Bootstrapping plugin.');
                $pm->bootstrap();
            }

        } catch (IfwPsn_Wp_Plugin_Loader_Exception $e) {
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
     * @param IfwPsn_Wp_Plugin_Loader_Abstract $loader
     */
    protected static function _handleError($error, IfwPsn_Wp_Plugin_Loader_Abstract $loader)
    {
        $logger = $loader->getLogger();
        if ($logger instanceof IfwPsn_Wp_Plugin_Logger) {
            $logger->err($error);
        }
        ifw_log_error($error);
    }
}
