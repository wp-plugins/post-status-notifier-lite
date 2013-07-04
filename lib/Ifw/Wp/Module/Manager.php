<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Module Manager
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp_Plugin_Admin
 */
class Ifw_Wp_Module_Manager
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
     * Retrieves singleton object
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     * @return Ifw_Wp_Module_Manager
     */
    public static function getInstance(Ifw_Wp_Plugin_Manager $pm)
    {
        if (!isset(self::$_instances[$pm->getAbbr()])) {
            self::$_instances[$pm->getAbbr()] = new self($pm);
        }
        return self::$_instances[$pm->getAbbr()];
    }

    /**
     *
     * @param Ifw_Wp_Plugin_Manager $pm
     */
    protected function __construct(Ifw_Wp_Plugin_Manager $pm)
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

                $dependencies = $module->getEnv()->getDependencies();

                if (empty($dependencies) || count(array_diff($dependencies, $this->_loaded)) == 0) {
                    try {
                        // init the module's default logic
                        $module->init();
                        // try to bootstrap module's custom logic
                        $module->bootstrap();

                        array_push($this->_loaded, $module->getEnv()->getId());

                        unset($modules[$i]);
                        $modules = array_values($modules);

                    } catch (Ifw_Wp_Model_Exception $e) {
                        $this->_pm->getLogger()->err('Module error: '. $e->getMessage());
                    } catch (Exception $e) {
                        $this->_pm->getLogger()->err('Unexpected exception in module "'. $module .'":'. $e->getMessage());
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
        if ($this->hasModules() && empty($this->_modules)) {
            foreach ($this->_getModulesFilenames() as $module) {
                try {
                    if ($this->_isValidModule($module)) {
                        $className = $this->_getModuleClassName($module);
                        $mod = new $className($this->_getModuleBootstrapPath($module), $this->_pm);
                        array_push($this->_modules, $mod);
                    }
                } catch (Ifw_Wp_Module_Exception $e) {
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
    protected function _getModulesFilenames()
    {
        if ($this->hasModules() && empty($this->_modulesFilenames)) {

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
     * @throws Ifw_Wp_Model_Exception
     * @return bool
     */
    protected function _isValidModule($module)
    {
        if (!file_exists($this->_getModuleBootstrapPath($module))) {
            throw new Ifw_Wp_Model_Exception('Missing bootstrap.php for module "'. $module . '"');
        }
        if (!file_exists($this->_getModuleXmlPath($module))) {
            throw new Ifw_Wp_Model_Exception('Missing module.xml for module "'. $module . '"');
        }

        require_once $this->_getModuleBootstrapPath($module);

        if (!class_exists($this->_getModuleClassName($module))) {
            throw new Ifw_Wp_Model_Exception('Invalid module class found for module "'. $module .'". Expecting: '.
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
        return $this->_pm->getPathinfo()->getRootModules() . $module . DIRECTORY_SEPARATOR . 'bootstrap.php';
    }

    /**
     * @param $module
     * @return string
     */
    protected function _getModuleXmlPath($module)
    {
        return $this->_pm->getPathinfo()->getRootModules() . $module . DIRECTORY_SEPARATOR . 'module.xml';
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
     * @return bool
     */
    public function hasModules()
    {
        return is_dir($this->_pm->getPathinfo()->getRootModules());
    }
}
