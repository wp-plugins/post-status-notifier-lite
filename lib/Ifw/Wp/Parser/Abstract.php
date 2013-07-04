<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Abstract parser
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
abstract class Ifw_Wp_Parser_Abstract implements Ifw_Wp_Parser_Interface
{
    /**
     * @var Ifw_Wp_Plugin_Logger
     */
    protected $_logger;
    
    /**
     * Set logger
     * @param Ifw_Wp_Plugin_Logger $logger
     */
    public function setLogger(Ifw_Wp_Plugin_Logger $logger)
    {
        $this->_logger = $logger;
    }
}
