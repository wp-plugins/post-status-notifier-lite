<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Wp_Proxy_Blog
{
    /**
     * @var
     */
    protected static $_themeData;



    public static function getCharset()
    {
        return get_bloginfo('charset');
    }

    public static function getName()
    {
        return get_bloginfo('blogname');
    }

    public static function getAdminEmail()
    {
        return get_bloginfo('admin_email');
    }

    public static function getLanguage()
    {
        return get_bloginfo('language');
    }

    public static function getGmtOffset()
    {
        return (float)get_option('gmt_offset');
    }

    public static function getVersion()
    {
        return get_bloginfo('version');
    }

    public static function isVersionGreaterThan($version)
    {
        return version_compare(self::getVersion(), $version) > 0;
    }

    public static function isMinimumVersion($version)
    {
        return version_compare(self::getVersion(), $version) >= 0;
    }

    public static function getUrl($path = null, $scheme = null)
    {
        return site_url($path, $scheme);
    }

    public static function getDateFormat()
    {
        return get_option('date_format');
    }

    public static function getTimeFormat()
    {
        return get_option('time_format');
    }

    public static function getTimezone()
    {
        return get_option('timezone_string');
    }

    public static function getThemeData()
    {
        if (empty(self::$_themeData)) {
            if (self::isMinimumVersion('3.4')) {
                self::$_themeData = wp_get_theme();
            } else {
                self::$_themeData = get_theme_data(get_stylesheet());
            }
        }

        return self::$_themeData;
    }

    /**
     * @return sting|null
     */
    public static function getThemeName()
    {
        $theme = self::getThemeData();
        return isset($theme['Name']) ? $theme['Name'] : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeURI()
    {
        $theme = self::getThemeData();
        return isset($theme['ThemeURI']) ? $theme['ThemeURI'] : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeDescription()
    {
        $theme = self::getThemeData();
        return isset($theme['Description']) ? $theme['Description'] : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeAuthor()
    {
        $theme = self::getThemeData();
        return isset($theme['Description']) ? $theme['Description'] : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeAuthorURI()
    {
        $theme = self::getThemeData();
        return isset($theme['AuthorURI']) ? $theme['AuthorURI'] : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeVersion()
    {
        $theme = self::getThemeData();
        return isset($theme['Version']) ? $theme['Version'] : null;
    }

    /**
     * The folder name of the current theme
     * @return sting|null
     */
    public static function getThemeTemplate()
    {
        $theme = self::getThemeData();
        return isset($theme['Template']) ? $theme['Template'] : null;
    }

    /**
     * If the theme is published
     *
     * @return sting|null
     */
    public static function getThemeStatus()
    {
        $theme = self::getThemeData();
        return isset($theme['Status']) ? $theme['Status'] : null;
    }

    /**
     * Tags used to describe the theme
     *
     * @return sting|null
     */
    public static function getThemeTags()
    {
        $theme = self::getThemeData();
        return isset($theme['Tags']) ? $theme['Tags'] : null;
    }

    /**
     * @param $plugin
     * @return bool
     */
    public static function isPluginActive($plugin)
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        return is_plugin_active($plugin);
    }

    /**
     * @return array
     */
    public static function getPlugins()
    {
        if (Ifw_Wp_Proxy_Action::didPluginsLoaded()) {
            return get_plugins();
        }
    }

    /**
     * @return string
     */
    public static function getLoginUrl()
    {
        return wp_login_url();
    }

    /**
     * @param $path
     * @param $scheme
     * @return string|void
     */
    public static function getSiteUrl($path = null, $scheme = null)
    {
        return site_url($path, $scheme);
    }

    /**
     * Checks if multisite / network is active
     * @return bool
     */
    public static function isMultisite()
    {
        return is_multisite();
    }

    /**
     * @return int
     */
    public static function getBlogId()
    {
        global $wpdb;
        return (int)$wpdb->blogid;
    }

    /**
     * @return array
     */
    public static function getMultisiteBlogIds()
    {
        global $wpdb;
        return $wpdb->get_col('SELECT blog_id FROM '. $wpdb->blogs);
    }

    /**
     * @param $blogId
     * @return bool
     */
    public static function switchToBlog($blogId)
    {
        return switch_to_blog($blogId);
    }
}
