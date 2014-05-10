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
class IfwPsn_Wp_Proxy_Blog
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

    /**
     * Retrieve the first language segment, like "en" or "de"
     * @return mixed
     */
    public static function getLanguageShort()
    {
        $lang = self::getLanguage();
        $langParts = explode('-', $lang);
        return $langParts[0];
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
        $result = $theme->get('Name');

        return !empty($result) ? $result : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeURI()
    {
        $theme = self::getThemeData();
        $result = $theme->get('ThemeURI');

        return !empty($result) ? $result : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeDescription()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Description');

        return !empty($result) ? $result : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeAuthor()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Author');

        return !empty($result) ? $result : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeAuthorURI()
    {
        $theme = self::getThemeData();
        $result = $theme->get('AuthorURI');

        return !empty($result) ? $result : null;
    }

    /**
     * @return sting|null
     */
    public static function getThemeVersion()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Version');

        return !empty($result) ? $result : null;
    }

    /**
     * The folder name of the current theme
     * @return sting|null
     */
    public static function getThemeTemplate()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Template');

        return !empty($result) ? $result : null;
    }

    /**
     * If the theme is published
     *
     * @return sting|null
     */
    public static function getThemeStatus()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Status');

        return !empty($result) ? $result : null;
    }

    /**
     * Tags used to describe the theme
     *
     * @return sting|null
     */
    public static function getThemeTags()
    {
        $theme = self::getThemeData();
        $result = $theme->get('Tags');

        return !empty($result) ? $result : null;
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
        $result = array();

        if (IfwPsn_Wp_Proxy_Action::didPluginsLoaded()) {
            $result = get_plugins();
        }

        return $result;
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

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @return string
     */
    public static function getServerEnvironment(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $tpl = IfwPsn_Wp_Tpl::getFilesytemInstance($pm);

        return $tpl->render('server_env.html.twig', array(
            'plugin_name' => $pm->getEnv()->getName(),
            'plugin_version' => $pm->getEnv()->getVersion(),
            'plugin_modules' => $pm->getBootstrap()->getModuleManager()->getModules(),
            'plugin_modules_initialized' => $pm->getBootstrap()->getModuleManager()->getInitializedModules(),
            'OS' => PHP_OS,
            'uname' => php_uname(),
            'wp_version' => IfwPsn_Wp_Proxy_Blog::getVersion(),
            'plugins' => IfwPsn_Wp_Proxy_Blog::getPlugins(),
            'theme_name' => IfwPsn_Wp_Proxy_Blog::getThemeName(),
            'theme_version' => IfwPsn_Wp_Proxy_Blog::getThemeVersion(),
            'theme_author' => IfwPsn_Wp_Proxy_Blog::getThemeAuthor(),
            'theme_uri' => IfwPsn_Wp_Proxy_Blog::getThemeURI(),
            'php_version' => phpversion(),
            'php_memory_limit' => ini_get('memory_limit'),
            'php_extensions' => IfwPsn_Wp_Server_Php::getExtensions(),
            'php_include_path' => get_include_path(),
            'php_open_basedir' => ini_get('open_basedir'),
            'mysql_version' => mysql_get_server_info(),
            'mysql_client' => mysql_get_client_info(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'apache_version' => apache_get_version(),
            'apache_modules' => apache_get_modules()
        ));
    }
}
