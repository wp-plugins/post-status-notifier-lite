<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Wp Script Proxy
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Proxy_Script
{
    /**
     * Container for scripts to enqueue
     * @var array
     */
    private static $_scripts = array();

    /**
     * Container for admin scripts to enqueue
     * @var array
     */
    private static $_scriptsAdmin = array();

    /**
     * Container for localize data
     * @var array
     */
    private static $_localize = array();

    /**
     * If enqueue function is set
     * @var bool
     */
    private static $_enqueueSet = false;

    /**
     * If admin enqueue function is set
     * @var bool
     */
    private static $_enqueueAdminSet = false;



    /**
     * @see wp_register_script() for parameter information
     */
    public static function register($handle, $src, $deps=array(), $ver=false, $in_footer=false)
    {
        wp_register_script($handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * @see WP_Scripts::remove() wp_localize_script
     */
    public static function deregister($handle)
    {
        wp_deregister_script($handle);
    }

    /**
     * @see wp_enqueue_script() for parameter information
     */
    public static function enqueue($handle, $src=false, $deps=array(), $ver=false, $in_footer=false)
    {
        wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * @param $handle
     */
    public static function dequeue($handle)
    {
        wp_dequeue_script($handle);
    }

    /**
     * Registers and enqueues a js file in one process
     * @param string $handle
     * @param bool|string $src
     * @param array $deps
     * @param bool $ver
     * @param bool $in_footer
     * @param bool $localize
     * @return void
     */
    public static function load($handle, $src=false, $deps=array(), $ver=false, $in_footer=false, $localize=false)
    {
        if (!isset(self::$_scripts[$handle])) {
            self::$_scripts[$handle] = array(
                'src' => $src,
                'deps' => $deps,
                'ver' => $ver,
                'in_footer' => $in_footer
            );
        }

        if (is_array($localize)) {
            self::localize($handle, key($localize), array_values($localize));
        }

        if (self::$_enqueueSet == false) {
            Ifw_Wp_Proxy_Action::addEnqueueScripts(array('Ifw_Wp_Proxy_Script', '_enqueueScripts'));
            self::$_enqueueSet = true;
        }
    }

    /**
     * @param $handle
     * @param bool $src
     * @param array $deps
     * @param bool $ver
     * @param bool $in_footer
     * @param bool $localize
     * @return void
     */
    public static function loadAdmin($handle, $src=false, $deps=array(), $ver=false, $in_footer=false, $localize=false)
    {
        if (!isset(self::$_scriptsAdmin[$handle])) {
            self::$_scriptsAdmin[$handle] = array(
                'src' => $src,
                'deps' => $deps,
                'ver' => $ver,
                'in_footer' => $in_footer
            );
        }

        if (is_array($localize)) {
            self::localize($handle, key($localize), array_values($localize));
        }

        if (self::$_enqueueAdminSet == false) {
            Ifw_Wp_Proxy_Action::addAdminEnqueueScripts(array('Ifw_Wp_Proxy_Script', '_enqueueAdminScripts'));
            self::$_enqueueAdminSet = true;
        }
    }

    /**
     * @see wp_localize_script() for parameter information
     */
    public static function localize($handle, $object_name, $l10n)
    {
        if (!isset(self::$_localize[$handle])) {
            self::$_localize[$handle] = array();
        }

        if (!isset(self::$_localize[$handle][$object_name])) {
            self::$_localize[$handle][$object_name] = $l10n;
        } else {
            self::$_localize[$handle][$object_name] = array_merge(self::$_localize[$handle][$object_name], $l10n);
        }
    }

    /**
     * Finally enqueues the script at the right moment (action)
     */
    public static function _enqueueScripts()
    {
        foreach (self::$_scripts as $handle => $data) {
            self::enqueue($handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer']);
            if (isset(self::$_localize[$handle])) {
                foreach (self::$_localize[$handle] as $object_name => $l10n) {
                    wp_localize_script($handle, $object_name, $l10n);
                }
            }
        }
    }

    /**
     * Finally enqueues the script at the right moment (action)
     */
    public static function _enqueueAdminScripts()
    {
        foreach (self::$_scriptsAdmin as $handle => $data) {
            self::enqueue($handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer']);
            if (isset(self::$_localize[$handle])) {
                foreach (self::$_localize[$handle] as $object_name => $l10n) {
                    wp_localize_script($handle, $object_name, $l10n);
                }
            }
        }
    }
}