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
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** IfwZend_View_Helper_Abstract.php */
//require_once 'IfwZend/View/Helper/Abstract.php';

/**
 * Currency view helper
 *
 * @category  Zend
 * @package   IfwZend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_View_Helper_Currency extends IfwZend_View_Helper_Abstract
{
    /**
     * Currency object
     *
     * @var IfwZend_Currency
     */
    protected $_currency;

    /**
     * Constructor for manually handling
     *
     * @param  IfwZend_Currency $currency Instance of IfwZend_Currency
     * @return void
     */
    public function __construct($currency = null)
    {
        if ($currency === null) {
            //require_once 'IfwZend/Registry.php';
            if (IfwZend_Registry::isRegistered('IfwZend_Currency')) {
                $currency = IfwZend_Registry::get('IfwZend_Currency');
            }
        }

        $this->setCurrency($currency);
    }

    /**
     * Output a formatted currency
     *
     * @param  integer|float                    $value    Currency value to output
     * @param  string|IfwZend_Locale|IfwZend_Currency $currency OPTIONAL Currency to use for this call
     * @return string Formatted currency
     */
    public function currency($value = null, $currency = null)
    {
        if ($value === null) {
            return $this;
        }

        if (is_string($currency) || ($currency instanceof IfwZend_Locale)) {
            //require_once 'IfwZend/Locale.php';
            if (IfwZend_Locale::isLocale($currency)) {
                $currency = array('locale' => $currency);
            }
        }

        if (is_string($currency)) {
            $currency = array('currency' => $currency);
        }

        if (is_array($currency)) {
            return $this->_currency->toCurrency($value, $currency);
        }

        return $this->_currency->toCurrency($value);
    }

    /**
     * Sets a currency to use
     *
     * @param  IfwZend_Currency|String|IfwZend_Locale $currency Currency to use
     * @throws IfwZend_View_Exception When no or a false currency was set
     * @return IfwZend_View_Helper_Currency
     */
    public function setCurrency($currency = null)
    {
        if (!$currency instanceof IfwZend_Currency) {
            //require_once 'IfwZend/Currency.php';
            $currency = new IfwZend_Currency($currency);
        }
        $this->_currency = $currency;

        return $this;
    }

    /**
     * Retrieve currency object
     *
     * @return IfwZend_Currency|null
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
}
