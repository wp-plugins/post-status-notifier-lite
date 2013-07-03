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
 * Resource for settings layout options
 *
 * @uses       IfwZend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    IfwZend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Application_Resource_Layout
    extends IfwZend_Application_Resource_ResourceAbstract
{
    /**
     * @var IfwZend_Layout
     */
    protected $_layout;

    /**
     * Defined by IfwZend_Application_Resource_Resource
     *
     * @return IfwZend_Layout
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('FrontController');
        return $this->getLayout();
    }

    /**
     * Retrieve layout object
     *
     * @return IfwZend_Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = IfwZend_Layout::startMvc($this->getOptions());
        }
        return $this->_layout;
    }
}
