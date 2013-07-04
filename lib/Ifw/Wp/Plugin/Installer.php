<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Plugin installer
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   Ifw_Wp_Plugin
 */
class Ifw_Wp_Plugin_Installer
{
    /**
     * Instance store
     * @var array
     */
    public static $_instances = array();
    
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var array
     */
    protected $_activation = array();

    /**
     * @var array
     */
    protected $_deactivation = array();

    /**
     * @var array
     */
    protected static $_uninstall = array();



    /**
     * Retrieves singleton Ifw_Wp_Plugin_Admin object
     * 
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Plugin_Installer
    */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!isset(self::$_instances[$pm->getAbbr()])) {
            self::$_instances[$pm->getAbbr()] = new self($pm);
        }
        return self::$_instances[$pm->getAbbr()];
    }

    /**
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    protected function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_initActivation();
        $this->_initDeactivation();
        $this->_initUninstall();
    }

    protected function _initActivation()
    {
        $this->registerActivation();
    }

    protected function _initDeactivation()
    {
        $this->registerDeactivation();
    }

    protected function _initUninstall()
    {
        self::$_uninstall[$this->_pm->getPathinfo()->getFilenamePath()] = array();
        $this->registerUninstall();
        // add default uninstall commands
        $this->addUninstall(new Ifw_Wp_Plugin_Installer_Command_UninstallDeleteLog());
        $this->addUninstall(new Ifw_Wp_Plugin_Installer_Command_UninstallResetOptions());
    }

    /**
     * Add the register_activation_hook
     */
    public function registerActivation()
    {
        register_activation_hook($this->_pm->getPathinfo()->getFilenamePath(), array($this, 'activate'));
    }
    
    /**
     * 
     * @param Ifw_Wp_Plugin_Installer_ActivationInterface $activation
     */
    public function addActivation(Ifw_Wp_Plugin_Installer_ActivationInterface $activation)
    {
        array_push($this->_activation, $activation);
    }

    /**
     * Loop over all added activation objects
     */
    public function activate()
    {
        /**
         * @var $activaion Ifw_Wp_Plugin_Installer_ActivationInterface
         */
        foreach ($this->_activation as $activaion) {
            $activaion->execute($this->_pm);
        }
    }

    /**
     * Add the register_activation_hook
     */
    public function registerDeactivation()
    {
        register_deactivation_hook($this->_pm->getPathinfo()->getFilenamePath(), array($this, 'deactivate'));
    }

    /**
     * 
     * @param Ifw_Wp_Plugin_Installer_DeactivationInterface $deactivation
     */
    public function addDeactivation(Ifw_Wp_Plugin_Installer_DeactivationInterface $deactivation)
    {
        array_push($this->_deactivation, $deactivation);
    }

    /**
     * Loop over all added deactivation objects
     */
    public function deactivate()
    {
        /**
         * @var $activaion Ifw_Wp_Plugin_Installer_DeactivationInterface
         */
        foreach ($this->_deactivation as $deactivaion) {
            $deactivaion->execute($this->_pm);
        }
    }

    /**
     *
     */
    public function registerUninstall()
    {
        register_uninstall_hook($this->_pm->getPathinfo()->getFilenamePath(), 'Ifw_Wp_Plugin_Installer::uninstall');
    }
    
    /**
     * 
     * @param Ifw_Wp_Plugin_Installer_UninstallInterface $uninstall
     */
    public function addUninstall(Ifw_Wp_Plugin_Installer_UninstallInterface $uninstall)
    {
        array_push(self::$_uninstall[$this->_pm->getPathinfo()->getFilenamePath()], $uninstall);
    }

    /**
     * @internal param \Ifw_Wp_Plugin_Installer_UninstallInterface $uninstall
     */
    public static function uninstall()
    {
        $filenamePath = array_shift(array_values($_GET['checked']));
        $pm = Ifw_Wp_Plugin_Manager::getInstanceFromFilenamePath($filenamePath);

        foreach(self::$_uninstall[$filenamePath] as $uninstall) {
            call_user_func(get_class($uninstall) . '::execute', $pm);
        }
    }
}