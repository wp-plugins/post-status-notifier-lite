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
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Application_Resource_ResourceAbstract
 */
//require_once 'IfwZend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for creating database adapter
 *
 * @uses       IfwZend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    IfwZend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Application_Resource_Db extends IfwZend_Application_Resource_ResourceAbstract
{
    /**
     * Adapter to use
     *
     * @var string
     */
    protected $_adapter = null;

    /**
     * @var IfwZend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Parameters to use
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Wether to register the created adapter as default table adapter
     *
     * @var boolean
     */
    protected $_isDefaultTableAdapter = true;

    /**
     * Set the adapter
     *
     * @param  string $adapter
     * @return IfwZend_Application_Resource_Db
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Adapter type to use
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set the adapter params
     *
     * @param  string $adapter
     * @return IfwZend_Application_Resource_Db
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Adapter parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set whether to use this as default table adapter
     *
     * @param  boolean $defaultTableAdapter
     * @return IfwZend_Application_Resource_Db
     */
    public function setIsDefaultTableAdapter($isDefaultTableAdapter)
    {
        $this->_isDefaultTableAdapter = $isDefaultTableAdapter;
        return $this;
    }

    /**
     * Is this adapter the default table adapter?
     *
     * @return void
     */
    public function isDefaultTableAdapter()
    {
        return $this->_isDefaultTableAdapter;
    }

    /**
     * Retrieve initialized DB connection
     *
     * @return null|IfwZend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        if ((null === $this->_db)
            && (null !== ($adapter = $this->getAdapter()))
        ) {
            $this->_db = IfwZend_Db::factory($adapter, $this->getParams());
        }
        return $this->_db;
    }

    /**
     * Defined by IfwZend_Application_Resource_Resource
     *
     * @return IfwZend_Db_Adapter_Abstract|null
     */
    public function init()
    {
        if (null !== ($db = $this->getDbAdapter())) {
            if ($this->isDefaultTableAdapter()) {
                IfwZend_Db_Table::setDefaultAdapter($db);
            }
            return $db;
        }
    }

    /**
     * Set the default metadata cache
     *
     * @param string|IfwZend_Cache_Core $cache
     * @return IfwZend_Application_Resource_Db
     */
    public function setDefaultMetadataCache($cache)
    {
        $metadataCache = null;

        if (is_string($cache)) {
            $bootstrap = $this->getBootstrap();
            if ($bootstrap instanceof IfwZend_Application_Bootstrap_ResourceBootstrapper
                && $bootstrap->hasPluginResource('CacheManager')
            ) {
                $cacheManager = $bootstrap->bootstrap('CacheManager')
                    ->getResource('CacheManager');
                if (null !== $cacheManager && $cacheManager->hasCache($cache)) {
                    $metadataCache = $cacheManager->getCache($cache);
                }
            }
        } else if ($cache instanceof IfwZend_Cache_Core) {
            $metadataCache = $cache;
        }

        if ($metadataCache instanceof IfwZend_Cache_Core) {
            IfwZend_Db_Table::setDefaultMetadataCache($metadataCache);
        }

        return $this;
    }
}
