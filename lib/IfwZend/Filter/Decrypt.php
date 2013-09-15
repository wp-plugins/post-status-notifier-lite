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
 * @see IfwZend_Filter_Encrypt
 */
//require_once 'IfwZend/Filter/Encrypt.php';

/**
 * Decrypts a given string
 *
 * @category   Zend
 * @package    IfwZend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Filter_Decrypt extends IfwZend_Filter_Encrypt
{
    /**
     * Defined by IfwZend_Filter_Interface
     *
     * Decrypts the content $value with the defined settings
     *
     * @param  string $value Content to decrypt
     * @return string The decrypted content
     */
    public function filter($value)
    {
        return $this->_adapter->decrypt($value);
    }
}
