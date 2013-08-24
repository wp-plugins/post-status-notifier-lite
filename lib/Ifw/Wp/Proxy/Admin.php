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
class Ifw_Wp_Proxy_Admin
{
    /**
     * Alias for get_admin_url()
     *
     * @param null|string $blog_id
     * @param string $blog_id
     * @param string $scheme
     * @return string
     */
    public static function getUrl($blog_id = null, $blog_id = '', $scheme = 'admin')
    {
        return get_admin_url($blog_id, $blog_id, $scheme);
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     * @param $controller
     * @param string $action
     * @param null $page
     * @param array $extra
     * @return string
     */
    public static function getMenuUrl(Ifw_Wp_Plugin_Manager $pm, $controller, $action='index', $page=null, $extra = array())
    {
        if ($page == null) {
            $page = $pm->getPathinfo()->getDirname();
        }

        $urlOptions = array_merge(array(
            'controller' => $controller,
            'action' => $action,
            'page' => $page
        ), $extra);

        $router = Ifw_Zend_Controller_Front::getInstance()->initRouter($pm)->getRouter();
        return $router->assemble($urlOptions, 'requestVars');
    }


    public static function getOptionsBaseUrl()
    {
        return 'options-general.php';
    }

    public static function getAdminPageBaseUrl()
    {
        return 'admin.php';
    }
}
