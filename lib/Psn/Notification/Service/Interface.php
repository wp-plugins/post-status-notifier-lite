<?php
/**
 * Notification service interface
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Notification
 */
interface Psn_Notification_Service_Interface
{
    public function execute(Psn_Model_Rule $rule, $post);
}
