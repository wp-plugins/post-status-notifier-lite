<?php
/**
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package
 */
interface Ifw_Wp_Plugin_Selftest_Interface
{
    /**
     * Gets the test name
     * @return mixed
     */
    public function getName();

    /**
     * Gets the test description
     * @return mixed
     */
    public function getDescription();

    /**
     * Runs the test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function execute(Ifw_Wp_Plugin_Manager $pm);

    /**
     * Gets the test result, true on success, false on failure
     * @return bool
     */
    public function getResult();

    /**
     * Gets the error message
     * @return mixed
     */
    public function getErrorMessage();

    /**
     * @return bool
     */
    public function canHandle();

    /**
     * Handles an error, should provide a solution for an unsuccessful test
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return mixed
     */
    public function handleError(Ifw_Wp_Plugin_Manager $pm);

}
