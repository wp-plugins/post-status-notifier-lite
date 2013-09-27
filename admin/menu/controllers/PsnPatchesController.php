<?php
/**
 * Patches controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class PsnPatchesController extends PsnApplicationController
{

    public function indexAction()
    {
        $this->view->executeUrl = Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'patches', 'execute');
    }

    public function executeAction()
    {
        $updateManager = $this->_pm->getBootstrap()->getUpdateManager();
        $patcher = $updateManager->getPatcher();

        $this->view->patcher = $patcher;
        $this->view->updateManager = $this->_pm->getBootstrap()->getUpdateManager();
        $this->view->proceedUrl = Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'patches', 'proceed');
        $this->view->continueUrl = Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'index');

    }

    public function proceedAction()
    {
        $this->_pm->getBootstrap()->getUpdateManager()->refreshPresentVersion();
        $this->view->continueUrl = Ifw_Wp_Proxy_Admin::getMenuUrl($this->_pm, 'index');
    }
}
