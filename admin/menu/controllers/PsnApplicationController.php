<?php
/**
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class PsnApplicationController extends Ifw_Zend_Application_DefaultController
{
    /**
     * Defines main navigation items
     */
    protected function _loadNavigationPages()
    {
        $nav = new Psn_Admin_Navigation($this->_pm);

        $this->_navigation = $nav->getNavigation();
    }

}
