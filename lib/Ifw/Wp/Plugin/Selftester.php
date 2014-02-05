<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Performs registered plugin tests
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */ 
class Ifw_Wp_Plugin_Selftester 
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    private $_pm;

    /**
     * @var array
     */
    private $_testCases = array();

    /**
     * @var bool
     */
    private $_status = true;

    /**
     * @var string
     */
    private $_timestampOptionName = 'selftest_timestamp';

    /**
     * @var string
     */
    private $_statusOptionName = 'selftest_status';



    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_init();
    }

    protected function _init()
    {
        $this->_registerBuiltinTests();
    }

    /**
     * Registers the built-in tests
     */
    protected function _registerBuiltinTests()
    {
        $this->addTestCase(new Ifw_Wp_Plugin_Selftest_Case_WpVersion());
    }

    public function activate()
    {
        Ifw_Wp_Proxy_Action::doPlugin($this->_pm, 'selftester_activate', $this);

        $this->_pm->getBootstrap()->getOptionsManager()->registerExternalOption($this->_timestampOptionName);
        $this->_pm->getBootstrap()->getOptionsManager()->registerExternalOption($this->_statusOptionName);

        if (!$this->_skipAutorun()) {
            $this->_initAutorun();
        }
    }

    /**
     *
     */
    protected function _initAutorun()
    {
        $interval = $this->_pm->getConfig()->plugin->selftestInterval;

        if (!empty($interval)) {
            if ($this->getTimestamp() == null ||
                Ifw_Wp_Date::isOlderThanSeconds($this->getTimestamp(), $interval)) {
                // perform a selftest if no one was run before or the selftest interval is exceeded
                Ifw_Wp_Proxy_Action::addPluginsLoaded(array($this, 'performTests'));
            }
        }
    }

    /**
     * @param Ifw_Wp_Plugin_Selftest_Interface $test
     */
    public function addTestCase(Ifw_Wp_Plugin_Selftest_Interface $test)
    {
        $this->_testCases[md5(get_class($test))] = $test;
    }

    /**
     * @return array
     */
    public function getTestCases()
    {
        return $this->_testCases;
    }

    /**
     * @param $key
     * @return null
     */
    public function getTest($key)
    {
        if (isset($this->_testCases[$key])) {
            return $this->_testCases[$key];
        }
        return null;
    }

    /**
     * Performs all registered tests
     */
    public function performTests()
    {
        /**
         * @var $test Ifw_Wp_Plugin_Selftest_Interface
         */
        foreach($this->_testCases as $test) {

            $test->execute($this->_pm);

            if (!$test->getResult()) {
                $this->_status = false;
            }
        }

        $this->_updateStatus();
        $this->_updateTimestamp();
    }

    /**
     * Updates the status of the last test
     */
    protected function _updateStatus()
    {
        $this->_pm->getBootstrap()->getOptionsManager()->updateOption($this->_statusOptionName, $this->_status);
    }

    /**
     * Retrieves the status of the last test
     * @return boolean
     */
    public function getStatus()
    {
        return $this->_pm->getBootstrap()->getOptionsManager()->getOption($this->_statusOptionName);
    }

    /**
     * Updates the timestamp of the last test
     */
    public function _updateTimestamp()
    {
        $this->_pm->getBootstrap()->getOptionsManager()->updateOption($this->_timestampOptionName, gmdate('Y-m-d H:i:s'));
    }

    /**
     * Retrieves the timestamp of the last test
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->_pm->getBootstrap()->getOptionsManager()->getOption($this->_timestampOptionName);
    }

    /**
     * @return bool
     */
    protected function _skipAutorun()
    {
        if ($this->_pm->getBootstrap()->getUpdateManager()->getPatcher()->isPatchesAvailable()) {
            return true;
        }

        return false;
    }
}
