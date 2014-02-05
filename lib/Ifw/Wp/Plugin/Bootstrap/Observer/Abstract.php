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
abstract class Ifw_Wp_Plugin_Bootstrap_Observer_Abstract implements Ifw_Wp_Plugin_Bootstrap_Observer_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Bootstrap_Abstract
     */
    protected $_bootstrap;

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var mixed
     */
    protected $_resource;


    /**
     * @param $notificationType
     * @param Ifw_Wp_Plugin_Bootstrap_Abstract $bootstrap
     * @return mixed
     */
    public function notify($notificationType, Ifw_Wp_Plugin_Bootstrap_Abstract $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
        $this->_pm = $bootstrap->getPluginManager();

        switch($notificationType) {
            case Ifw_Wp_Plugin_Bootstrap_Abstract::OBSERVER_PRE_BOOTSTRAP:
                if (method_exists($this, '_preBootstrap')) {
                    $this->_preBootstrap();
                }
                break;

            case Ifw_Wp_Plugin_Bootstrap_Abstract::OBSERVER_POST_BOOTSTRAP:
                if (method_exists($this, '_postBootstrap')) {
                    $this->_postBootstrap();
                }
                break;

            case Ifw_Wp_Plugin_Bootstrap_Abstract::OBSERVER_SHUTDOWN_BOOTSTRAP:
                if (method_exists($this, '_shutdownBootstrap')) {
                    $this->_shutdownBootstrap();
                }
                break;

        }
    }

    /**
     * @return Ifw_Wp_Plugin_Installer
     */
    public function getResource()
    {
        return $this->_resource;
    }
}
