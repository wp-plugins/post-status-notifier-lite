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
class Ifw_Wp_Plugin_Selftest_Case_WpVersion implements Ifw_Wp_Plugin_Selftest_Interface
{
    /**
     * @var bool
     */
    protected $_result = false;

    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;



    /**
     * Gets the test name
     * @return mixed
     */
    public function getName()
    {
        return __('WP Version', 'ifw');
    }

    /**
     * Gets the test description
     * @return mixed
     */
    public function getDescription()
    {
        return __('Checks if the WordPress version is supported', 'ifw');
    }

    /**
     * Runs the test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_result = Ifw_Wp_Proxy_Blog::isMinimumVersion($pm->getConfig()->plugin->wpMinVersion);
    }

    /**
     * Gets the test result, true on success, false on failure
     * @return bool
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Gets the error message
     * @return mixed
     */
    public function getErrorMessage()
    {
        return sprintf(
            __('Your WordPress version is not supported. Please upgrade to at least version %s', 'ifw'),
            $this->_pm->getConfig()->plugin->wpMinVersion
        );
    }

    /**
     * @return bool
     */
    public function canHandle()
    {
        return false;
    }

    /**
     * Handles an error, should provide a solution for an unsuccessful test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function handleError(Ifw_Wp_Plugin_Manager $pm)
    {
        // nothing we can do, user must upgrade WP
    }

}
