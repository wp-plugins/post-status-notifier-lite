<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Loggable interface
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Interface
 */
interface Ifw_Wp_Interface_Loggable
{
    /**
     * Sets logger
     * @param Ifw_Wp_Plugin_Logger $logger
     */
    public function setLogger(Ifw_Wp_Plugin_Logger $logger);
    
    /**
     * Retrieves logger
     * @return null|Ifw_Wp_Plugin_Logger
     */
    public function getLogger();
}
?>