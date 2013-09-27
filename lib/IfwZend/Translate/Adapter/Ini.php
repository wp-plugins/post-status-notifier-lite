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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwZend_Locale */
//require_once 'IfwZend/Locale.php';

/** IfwZend_Translate_Adapter */
//require_once 'IfwZend/Translate/Adapter.php';

/**
 * @category   Zend
 * @package    IfwZend_Translate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Translate_Adapter_Ini extends IfwZend_Translate_Adapter
{
    private $_data = array();

    /**
     * Load translation data
     *
     * @param  string|array  $data
     * @param  string        $locale  Locale/Language to add data for, identical with locale identifier,
     *                                see IfwZend_Locale for more information
     * @param  array         $options OPTIONAL Options to use
     * @throws IfwZend_Translate_Exception Ini file not found
     * @return array
     */
    protected function _loadTranslationData($data, $locale, array $options = array())
    {
        $this->_data = array();
        if (!file_exists($data)) {
            //require_once 'IfwZend/Translate/Exception.php';
            throw new IfwZend_Translate_Exception("Ini file '".$data."' not found");
        }

        $inidata = parse_ini_file($data, false);
        if (!isset($this->_data[$locale])) {
            $this->_data[$locale] = array();
        }

        $this->_data[$locale] = array_merge($this->_data[$locale], $inidata);
        return $this->_data;
    }

    /**
     * returns the adapters name
     *
     * @return string
     */
    public function toString()
    {
        return "Ini";
    }
}
