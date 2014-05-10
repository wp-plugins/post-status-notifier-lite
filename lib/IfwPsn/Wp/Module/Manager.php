<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Module Manager
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  IfwPsn_Wp_Plugin_Admin
 */
class IfwPsn_Wp_Module_Manager
{
    /**
     * Module instance store
     * @var array
     */
    public $_instances = array();

    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var array
     */
    protected $_modulesFilenames = array();

    /**
     * @var array
     */
    protected $_modules = array();

    /**
     * @var array
     */
    protected $_loaded = array();

    /**
     * @var array
     */
    protected $_bootstrapPath = array();

    /**
     * @var null|bool
     */
    protected $_hasModules;




    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * Module loading loop
     */
    public function load()
    {
        $modules = $this->getModules();
        $loop = 0;

        while(!empty($modules) && $loop < 10) {

            for($i = 0; $i < count($modules); $i++) {

                $module = $modules[$i];

                $dependencies = $module->getDependencies();

                if (empty($dependencies) || count(array_diff($dependencies, $this->_loaded)) == 0) {
                    try {
                        // init the module's default logic
                        $module->init();
                        // try to bootstrap module's custom logic
                        $module->bootstrap();

                        array_push($this->_loaded, $module->getId());

                        if (!isset($this->_instances[$module->getId()])) {
                            $this->_instances[$module->getId()] = $module;
                        }

                        unset($modules[$i]);
                        $modules = array_values($modules);

                    } catch (IfwPsn_Wp_Model_Exception $e) {
                        $this->_pm->getLogger()->err('Module error: '. $e->getMessage());
                    } catch (Exception $e) {
                        $this->_pm->getLogger()->err('Unexpected exception in module "'. $module->getId() .'":'. $e->getMessage());
                    }
                }
            }

            $loop++;
        }
    }

    /**
     *  Registers the module to front controller
     */
    public function registerModules()
    {
        foreach ($this->getModules() as $module) {
            $module->registerPath();
        }
    }

    /**
     * @return array
     */
    public function getModules()
    {
        if ($this->_pm->getPathinfo()->hasModulesDir() && empty($this->_modules)) {

            foreach ($this->_getModulesFilenames() as $module) {
                try {
                    if ($this->_isValidModule($module)) {
                        $className = $this->_getModuleClassName($module);
                        $mod = new $className($this->_getModuleBootstrapPath($module), $this->_pm);
                        array_push($this->_modules, $mod);
                    }
                } catch (IfwPsn_Wp_Module_Exception $e) {
                    $this->_pm->getLogger()->err('Module error: '. $e->getMessage());
                } catch (Exception $e) {
                    $this->_pm->getLogger()->err('Unexpected exception in module "'. $module .'":'. $e->getMessage());
                }
            }
        }

        return $this->_modules;
    }

    /**
     * @return array
     */
    public function getInitializedModules()
    {
        $result = array();

        /**
         * @var IfwPsn_Wp_Module_Bootstrap_Abstract $module
         */
        foreach ($this->getModules() as $module) {
            if ($module->isInitialized()) {
                array_push($result, $module);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getModulesFilenames()
    {
        if ($this->_pm->getPathinfo()->hasModulesDir() && empty($this->_modulesFilenames)) {

            $modulesDir = new DirectoryIterator($this->_pm->getPathinfo()->getRootModules());

            foreach ($modulesDir as $fileinfo) {

                if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                    continue;
                }

                array_push($this->_modulesFilenames, $fileinfo->getFilename());
            }

        }

        return $this->_modulesFilenames;
    }

    /**
     * @param $module
     * @throws IfwPsn_Wp_Module_Exception
     * @throws IfwPsn_Wp_Model_Exception
     * @return bool
     */
    protected function _isValidModule($module)
    {
        require_once $this->_pm->getPathinfo()->getRootLib() . '/IfwPsn/Wp/Module/Bootstrap/Abstract.php';
        require_once $this->_getModuleBootstrapPath($module);

        if (!class_exists($this->_getModuleClassName($module))) {
            throw new IfwPsn_Wp_Model_Exception('Invalid module class found for module "'. $module .'". Expecting: '.
                $this->_getModuleClassName($module));
        }

        return true;
    }

    /**
     * @param $module
     * @return string
     */
    protected function _getModuleBootstrapPath($module)
    {
        if (!isset($this->_bootstrapPath[$module])) {
            $this->_bootstrapPath[$module] = $this->_pm->getPathinfo()->getRootModules() . $module . DIRECTORY_SEPARATOR . 'bootstrap.php';
        }

        return $this->_bootstrapPath[$module];
    }

    /**
     * @param $module
     * @return string
     */
    protected function _getModuleClassName($module)
    {
        return $module . '_Bootstrap';
    }

    /**
     * @param $id
     * @return IfwPsn_Wp_Module_Bootstrap_Abstract|null
     */
    public function getModule($id)
    {
        if (isset($this->_instances[$id])) {
            return $this->_instances[$id];
        }
        return null;
    }
}
