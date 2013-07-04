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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwZend_View_Helper_Placeholder_Registry */
//require_once 'IfwZend/View/Helper/Placeholder/Registry.php';

/** IfwZend_View_Helper_Abstract.php */
//require_once 'IfwZend/View/Helper/Abstract.php';

/**
 * Helper for passing data between otherwise segregated Views. It's called
 * Placeholder to make its typical usage obvious, but can be used just as easily
 * for non-Placeholder things. That said, the support for this is only
 * guaranteed to effect subsequently rendered templates, and of course Layouts.
 *
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_View_Helper_Placeholder extends IfwZend_View_Helper_Abstract
{
    /**
     * Placeholder items
     * @var array
     */
    protected $_items = array();

    /**
     * @var IfwZend_View_Helper_Placeholder_Registry
     */
    protected $_registry;

    /**
     * Constructor
     *
     * Retrieve container registry from IfwZend_Registry, or create new one and register it.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_registry = IfwZend_View_Helper_Placeholder_Registry::getRegistry();
    }


    /**
     * Placeholder helper
     *
     * @param  string $name
     * @return IfwZend_View_Helper_Placeholder_Container_Abstract
     */
    public function placeholder($name)
    {
        $name = (string) $name;
        return $this->_registry->getContainer($name);
    }

    /**
     * Retrieve the registry
     *
     * @return IfwZend_View_Helper_Placeholder_Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }
}
