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

/** IfwZend_View_Helper_Abstract.php */
//require_once 'IfwZend/View/Helper/Abstract.php';

/**
 * View helper for retrieving layout object
 *
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_View_Helper_Layout extends IfwZend_View_Helper_Abstract
{
    /** @var IfwZend_Layout */
    protected $_layout;

    /**
     * Get layout object
     *
     * @return IfwZend_Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            //require_once 'IfwZend/Layout.php';
            $this->_layout = IfwZend_Layout::getMvcInstance();
            if (null === $this->_layout) {
                // Implicitly creates layout object
                $this->_layout = new IfwZend_Layout();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  IfwZend_Layout $layout
     * @return IfwZend_Layout_Controller_Action_Helper_Layout
     */
    public function setLayout(IfwZend_Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Return layout object
     *
     * Usage: $this->layout()->setLayout('alternate');
     *
     * @return IfwZend_Layout
     */
    public function layout()
    {
        return $this->getLayout();
    }
}
