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
 * @package    IfwZend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Filter_Interface
 */
//require_once 'IfwZend/Filter/Interface.php';

/**
 * @see IfwZend_Loader
 */
//require_once 'IfwZend/Loader.php';

/**
 * Encrypts a given string
 *
 * @category   Zend
 * @package    IfwZend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Filter_Encrypt implements IfwZend_Filter_Interface
{
    /**
     * Encryption adapter
     */
    protected $_adapter;

    /**
     * Class constructor
     *
     * @param string|array $options (Optional) Options to set, if null mcrypt is used
     */
    public function __construct($options = null)
    {
        if ($options instanceof IfwZend_Config) {
            $options = $options->toArray();
        }

        $this->setAdapter($options);
    }

    /**
     * Returns the name of the set adapter
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->_adapter->toString();
    }

    /**
     * Sets new encryption options
     *
     * @param  string|array $options (Optional) Encryption options
     * @return IfwZend_Filter_Encrypt
     */
    public function setAdapter($options = null)
    {
        if (is_string($options)) {
            $adapter = $options;
        } else if (isset($options['adapter'])) {
            $adapter = $options['adapter'];
            unset($options['adapter']);
        } else {
            $adapter = 'Mcrypt';
        }

        if (!is_array($options)) {
            $options = array();
        }

        if (Ifw_Zend_Loader::isReadable('IfwZend/Filter/Encrypt/' . ucfirst($adapter). '.php')) {
            $adapter = 'IfwZend_Filter_Encrypt_' . ucfirst($adapter);
        }

        if (!class_exists($adapter)) {
            Ifw_Zend_Loader::loadClass($adapter);
        }

        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof IfwZend_Filter_Encrypt_Interface) {
            //require_once 'IfwZend/Filter/Exception.php';
            throw new IfwZend_Filter_Exception("Encoding adapter '" . $adapter . "' does not implement IfwZend_Filter_Encrypt_Interface");
        }

        return $this;
    }

    /**
     * Calls adapter methods
     *
     * @param string       $method  Method to call
     * @param string|array $options Options for this method
     */
    public function __call($method, $options)
    {
        $part = substr($method, 0, 3);
        if ((($part != 'get') and ($part != 'set')) or !method_exists($this->_adapter, $method)) {
            //require_once 'IfwZend/Filter/Exception.php';
            throw new IfwZend_Filter_Exception("Unknown method '{$method}'");
        }

        return call_user_func_array(array($this->_adapter, $method), $options);
    }

    /**
     * Defined by IfwZend_Filter_Interface
     *
     * Encrypts the content $value with the defined settings
     *
     * @param  string $value Content to encrypt
     * @return string The encrypted content
     */
    public function filter($value)
    {
        return $this->_adapter->encrypt($value);
    }
}