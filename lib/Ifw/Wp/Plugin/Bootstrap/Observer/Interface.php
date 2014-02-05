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
interface Ifw_Wp_Plugin_Bootstrap_Observer_Interface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param $notificationType
     * @param Ifw_Wp_Plugin_Bootstrap_Abstract $bootstrap
     * @return mixed
     */
    public function notify($notificationType, Ifw_Wp_Plugin_Bootstrap_Abstract $bootstrap);

    /**
     * @return mixed
     */
    public function getResource();
}
