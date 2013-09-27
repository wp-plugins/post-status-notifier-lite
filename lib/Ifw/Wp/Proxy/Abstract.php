<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * The abstract proxy class every proxy must extend
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
abstract class Ifw_Wp_Proxy_Abstract
{
    /**
     * If one proxy method needs another proxy
     * @var Ifw_Wp_Proxy
     */
    protected $_wpProxy;
    
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     *
     * @param Ifw_Wp_Proxy $wpProxy
     * @param Ifw_Wp_Plugin_Manager $pm
     * @internal param \Ifw_Wp_Proxy $wp_proxy
     */
    public function __construct (Ifw_Wp_Proxy $wpProxy, Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_wpProxy = $wpProxy;
        $this->_pm = $pm;
    }
}
