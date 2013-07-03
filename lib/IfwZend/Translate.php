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
 * @package    IfwZend_Translate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Loader
 */
//require_once 'IfwZend/Loader.php';

/**
 * @see IfwZend_Translate_Adapter
 */
//require_once 'IfwZend/Translate/Adapter.php';


/**
 * @category   Zend
 * @package    IfwZend_Translate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Translate {
    /**
     * Adapter names constants
     */
    const AN_ARRAY   = 'Array';
    const AN_CSV     = 'Csv';
    const AN_GETTEXT = 'Gettext';
    const AN_INI     = 'Ini';
    const AN_QT      = 'Qt';
    const AN_TBX     = 'Tbx';
    const AN_TMX     = 'Tmx';
    const AN_XLIFF   = 'Xliff';
    const AN_XMLTM   = 'XmlTm';

    const LOCALE_DIRECTORY = 'directory';
    const LOCALE_FILENAME  = 'filename';

    /**
     * Adapter
     *
     * @var IfwZend_Translate_Adapter
     */
    private $_adapter;

    /**
     * Generates the standard translation object
     *
     * @param  array|IfwZend_Config $options Options to use
     * @throws IfwZend_Translate_Exception
     */
    public function __construct($options = array())
    {
        if ($options instanceof IfwZend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('adapter' => $options);
        }

        $this->setAdapter($options);
    }

    /**
     * Sets a new adapter
     *
     * @param  array|IfwZend_Config $options Options to use
     * @throws IfwZend_Translate_Exception
     */
    public function setAdapter($options = array())
    {
        if ($options instanceof IfwZend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['adapter'] = array_shift($args);
            if (!empty($args)) {
                $options['content'] = array_shift($args);
            }

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('adapter' => $options);
        }

        if (Ifw_Zend_Loader::isReadable('IfwZend/Translate/Adapter/' . ucfirst($options['adapter']). '.php')) {
            $options['adapter'] = 'IfwZend_Translate_Adapter_' . ucfirst($options['adapter']);
        }

        if (!class_exists($options['adapter'])) {
            Ifw_Zend_Loader::loadClass($options['adapter']);
        }

        if (array_key_exists('cache', $options)) {
            IfwZend_Translate_Adapter::setCache($options['cache']);
        }

        $adapter = $options['adapter'];
        unset($options['adapter']);
        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof IfwZend_Translate_Adapter) {
            //require_once 'IfwZend/Translate/Exception.php';
            throw new IfwZend_Translate_Exception("Adapter " . $adapter . " does not extend IfwZend_Translate_Adapter");
        }
    }

    /**
     * Returns the adapters name and it's options
     *
     * @return IfwZend_Translate_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the set cache
     *
     * @return IfwZend_Cache_Core The set cache
     */
    public static function getCache()
    {
        return IfwZend_Translate_Adapter::getCache();
    }

    /**
     * Sets a cache for all instances of IfwZend_Translate
     *
     * @param  IfwZend_Cache_Core $cache Cache to store to
     * @return void
     */
    public static function setCache(IfwZend_Cache_Core $cache)
    {
        IfwZend_Translate_Adapter::setCache($cache);
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        return IfwZend_Translate_Adapter::hasCache();
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        IfwZend_Translate_Adapter::removeCache();
    }

    /**
     * Clears all set cache data
     *
     * @param string $tag Tag to clear when the default tag name is not used
     * @return void
     */
    public static function clearCache($tag = null)
    {
        IfwZend_Translate_Adapter::clearCache($tag);
    }

    /**
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        //require_once 'IfwZend/Translate/Exception.php';
        throw new IfwZend_Translate_Exception("Unknown method '" . $method . "' called!");
    }
}
