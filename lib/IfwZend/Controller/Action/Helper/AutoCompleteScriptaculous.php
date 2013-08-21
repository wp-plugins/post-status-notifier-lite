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
 * @package    IfwZend_Controller
 * @subpackage IfwZend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see IfwZend_Controller_Action_Helper_AutoComplete_Abstract
 */
//require_once 'IfwZend/Controller/Action/Helper/AutoComplete/Abstract.php';

/**
 * Create and send Scriptaculous-compatible autocompletion lists
 *
 * @uses       IfwZend_Controller_Action_Helper_AutoComplete_Abstract
 * @category   Zend
 * @package    IfwZend_Controller
 * @subpackage IfwZend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Controller_Action_Helper_AutoCompleteScriptaculous extends IfwZend_Controller_Action_Helper_AutoComplete_Abstract
{
    /**
     * Validate data for autocompletion
     *
     * @param  mixed $data
     * @return bool
     */
    public function validateData($data)
    {
        if (!is_array($data) && !is_scalar($data)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare data for autocompletion
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @throws IfwZend_Controller_Action_Exception
     * @return string
     */
    public function prepareAutoCompletion($data, $keepLayouts = false)
    {
        if (!$this->validateData($data)) {
            /**
             * @see IfwZend_Controller_Action_Exception
             */
            //require_once 'IfwZend/Controller/Action/Exception.php';
            throw new IfwZend_Controller_Action_Exception('Invalid data passed for autocompletion');
        }

        $data = (array) $data;
        $data = '<ul><li>' . implode('</li><li>', $data) . '</li></ul>';

        if (!$keepLayouts) {
            $this->disableLayouts();
        }

        return $data;
    }
}
