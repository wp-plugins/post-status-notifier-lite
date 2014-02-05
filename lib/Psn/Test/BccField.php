<?php
/**
 * Test for table field
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Psn_Test_BccField implements Ifw_Wp_Plugin_Selftest_Interface
{
    private $_result = false;



    /**
     * Gets the test name
     * @return mixed
     */
    public function getName()
    {
        return __('Bcc field', 'psn');
    }

    /**
     * Gets the test description
     * @return mixed
     */
    public function getDescription()
    {
        return __('Checks if the field exists in the database', 'psn');
    }

    /**
     * Runs the test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm)
    {
        if (Ifw_Wp_Proxy_Db::columnExists('psn_rules', 'bcc')) {
            $this->_result = true;
        }
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
        return __('The database field could not be found', 'psn');
    }

    /**
     * @return bool
     */
    public function canHandle()
    {
        return true;
    }

    /**
     * Handles an error, should provide a solution for an unsuccessful test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function handleError(Ifw_Wp_Plugin_Manager $pm)
    {
        $patcher = new Psn_Patch_Database();
        $patcher->createRulesFieldBcc();

        return __('Trying to create the field...', 'psn');
    }

}
