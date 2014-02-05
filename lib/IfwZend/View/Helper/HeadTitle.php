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

/** IfwZend_View_Helper_Placeholder_Container_Standalone */
//require_once 'IfwZend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving title element for HTML head
 *
 * @uses       IfwZend_View_Helper_Placeholder_Container_Standalone
 * @package    IfwZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_View_Helper_HeadTitle extends IfwZend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'IfwZend_View_Helper_HeadTitle';

    /**
     * Whether or not auto-translation is enabled
     * @var boolean
     */
    protected $_translate = false;

    /**
     * Translation object
     *
     * @var IfwZend_Translate_Adapter
     */
    protected $_translator;

    /**
     * Default title rendering order (i.e. order in which each title attached)
     *
     * @var string
     */
    protected $_defaultAttachOrder = null;

    /**
     * Retrieve placeholder for title element and optionally set state
     *
     * @param  string $title
     * @param  string $setType
     * @return IfwZend_View_Helper_HeadTitle
     */
    public function headTitle($title = null, $setType = null)
    {
        if (null === $setType) {
            $setType = (null === $this->getDefaultAttachOrder())
                     ? IfwZend_View_Helper_Placeholder_Container_Abstract::APPEND
                     : $this->getDefaultAttachOrder();
        }
        $title = (string) $title;
        if ($title !== '') {
            if ($setType == IfwZend_View_Helper_Placeholder_Container_Abstract::SET) {
                $this->set($title);
            } elseif ($setType == IfwZend_View_Helper_Placeholder_Container_Abstract::PREPEND) {
                $this->prepend($title);
            } else {
                $this->append($title);
            }
        }

        return $this;
    }

    /**
     * Set a default order to add titles
     *
     * @param string $setType
     */
    public function setDefaultAttachOrder($setType)
    {
        if (!in_array($setType, array(
            IfwZend_View_Helper_Placeholder_Container_Abstract::APPEND,
            IfwZend_View_Helper_Placeholder_Container_Abstract::SET,
            IfwZend_View_Helper_Placeholder_Container_Abstract::PREPEND
        ))) {
            //require_once 'IfwZend/View/Exception.php';
            throw new IfwZend_View_Exception("You must use a valid attach order: 'PREPEND', 'APPEND' or 'SET'");
        }

        $this->_defaultAttachOrder = $setType;
        return $this;
    }

    /**
     * Get the default attach order, if any.
     *
     * @return mixed
     */
    public function getDefaultAttachOrder()
    {
        return $this->_defaultAttachOrder;
    }

    /**
     * Sets a translation Adapter for translation
     *
     * @param  IfwZend_Translate|IfwZend_Translate_Adapter $translate
     * @return IfwZend_View_Helper_HeadTitle
     */
    public function setTranslator($translate)
    {
        if ($translate instanceof IfwZend_Translate_Adapter) {
            $this->_translator = $translate;
        } elseif ($translate instanceof IfwZend_Translate) {
            $this->_translator = $translate->getAdapter();
        } else {
            //require_once 'IfwZend/View/Exception.php';
            $e = new IfwZend_View_Exception("You must set an instance of IfwZend_Translate or IfwZend_Translate_Adapter");
            $e->setView($this->view);
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve translation object
     *
     * If none is currently registered, attempts to pull it from the registry
     * using the key 'IfwZend_Translate'.
     *
     * @return IfwZend_Translate_Adapter|null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            //require_once 'IfwZend/Registry.php';
            if (IfwZend_Registry::isRegistered('IfwZend_Translate')) {
                $this->setTranslator(IfwZend_Registry::get('IfwZend_Translate'));
            }
        }
        return $this->_translator;
    }

    /**
     * Enables translation
     *
     * @return IfwZend_View_Helper_HeadTitle
     */
    public function enableTranslation()
    {
        $this->_translate = true;
        return $this;
    }

    /**
     * Disables translation
     *
     * @return IfwZend_View_Helper_HeadTitle
     */
    public function disableTranslation()
    {
        $this->_translate = false;
        return $this;
    }

    /**
     * Turn helper into string
     *
     * @param  string|null $indent
     * @param  string|null $locale
     * @return string
     */
    public function toString($indent = null, $locale = null)
    {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = array();

        if($this->_translate && $translator = $this->getTranslator()) {
            foreach ($this as $item) {
                $items[] = $translator->translate($item, $locale);
            }
        } else {
            foreach ($this as $item) {
                $items[] = $item;
            }
        }

        $separator = $this->getSeparator();
        $output = '';
        if(($prefix = $this->getPrefix())) {
            $output  .= $prefix;
        }
        $output .= implode($separator, $items);
        if(($postfix = $this->getPostfix())) {
            $output .= $postfix;
        }

        $output = ($this->_autoEscape) ? $this->_escape($output) : $output;

        return $indent . '<title>' . $output . '</title>';
    }
}
