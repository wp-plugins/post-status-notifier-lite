<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwZend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Interface for bootstrap classes that utilize resource plugins
 *
 * @category   Zend
 * @package    IfwZend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface IfwZend_Application_Bootstrap_ResourceBootstrapper
{
    /**
     * Register a resource with the bootstrap
     *
     * @param  string|IfwZend_Application_Resource_Resource $resource
     * @param  null|array|IfwZend_Config                     $options
     * @return IfwZend_Application_Bootstrap_ResourceBootstrapper
     */
    public function registerPluginResource($resource, $options = null);

    /**
     * Unregister a resource from the bootstrap
     *
     * @param  string|IfwZend_Application_Resource_Resource $resource
     * @return IfwZend_Application_Bootstrap_ResourceBootstrapper
     */
    public function unregisterPluginResource($resource);

    /**
     * Is the requested resource registered?
     *
     * @param  string $resource
     * @return bool
     */
    public function hasPluginResource($resource);

    /**
     * Retrieve resource
     *
     * @param  string $resource
     * @return IfwZend_Application_Resource_Resource
     */
    public function getPluginResource($resource);

    /**
     * Get all resources
     *
     * @return array
     */
    public function getPluginResources();

    /**
     * Get just resource names
     *
     * @return array
     */
    public function getPluginResourceNames();

    /**
     * Set plugin loader to use to fetch resources
     *
     * @param  IfwZend_Loader_PluginLoader_Interface IfwZend_Loader_PluginLoader
     * @return IfwZend_Application_Bootstrap_ResourceBootstrapper
     */
    public function setPluginLoader(IfwZend_Loader_PluginLoader_Interface $loader);

    /**
     * Retrieve plugin loader for resources
     *
     * @return IfwZend_Loader_PluginLoader
     */
    public function getPluginLoader();
}
