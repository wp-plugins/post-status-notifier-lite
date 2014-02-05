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
 * @package    IfwZend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** IfwZend_Form_Decorator_Abstract */
//require_once 'IfwZend/Form/Decorator/Abstract.php';

/**
 * IfwZend_Form_Decorator_FormElements
 *
 * Render all form elements registered with current form
 *
 * Accepts following options:
 * - separator: Separator to use between elements
 *
 * Any other options passed will be used as HTML attributes of the form tag.
 *
 * @category   Zend
 * @package    IfwZend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class IfwZend_Form_Decorator_FormElements extends IfwZend_Form_Decorator_Abstract
{
    /**
     * Merges given two belongsTo (array notation) strings
     *
     * @param  string $baseBelongsTo
     * @param  string $belongsTo
     * @return string
     */
    public function mergeBelongsTo($baseBelongsTo, $belongsTo)
    {
        $endOfArrayName = strpos($belongsTo, '[');

        if ($endOfArrayName === false) {
            return $baseBelongsTo . '[' . $belongsTo . ']';
        }

        $arrayName = substr($belongsTo, 0, $endOfArrayName);

        return $baseBelongsTo . '[' . $arrayName . ']' . substr($belongsTo, $endOfArrayName);
    }

    /**
     * Render form elements
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $form    = $this->getElement();
        if ((!$form instanceof IfwZend_Form) && (!$form instanceof IfwZend_Form_DisplayGroup)) {
            return $content;
        }

        $belongsTo      = ($form instanceof IfwZend_Form) ? $form->getElementsBelongTo() : null;
        $elementContent = '';
        $displayGroups  = ($form instanceof IfwZend_Form) ? $form->getDisplayGroups() : array();
        $separator      = $this->getSeparator();
        $translator     = $form->getTranslator();
        $items          = array();
        $view           = $form->getView();
        foreach ($form as $item) {
            $item->setView($view)
                 ->setTranslator($translator);
            if ($item instanceof IfwZend_Form_Element) {
                foreach ($displayGroups as $group) {
                    $elementName = $item->getName();
                    $element     = $group->getElement($elementName);
                    if ($element) {
                        // Element belongs to display group; only render in that
                        // context.
                        continue 2;
                    }
                }
                $item->setBelongsTo($belongsTo);
            } elseif (!empty($belongsTo) && ($item instanceof IfwZend_Form)) {
                if ($item->isArray()) {
                    $name = $this->mergeBelongsTo($belongsTo, $item->getElementsBelongTo());
                    $item->setElementsBelongTo($name, true);
                } else {
                    $item->setElementsBelongTo($belongsTo, true);
                }
            } elseif (!empty($belongsTo) && ($item instanceof IfwZend_Form_DisplayGroup)) {
                foreach ($item as $element) {
                    $element->setBelongsTo($belongsTo);
                }
            }

            $items[] = $item->render();

            if (($item instanceof IfwZend_Form_Element_File)
                || (($item instanceof IfwZend_Form)
                    && (IfwZend_Form::ENCTYPE_MULTIPART == $item->getEnctype()))
                || (($item instanceof IfwZend_Form_DisplayGroup)
                    && (IfwZend_Form::ENCTYPE_MULTIPART == $item->getAttrib('enctype')))
            ) {
                if ($form instanceof IfwZend_Form) {
                    $form->setEnctype(IfwZend_Form::ENCTYPE_MULTIPART);
                } elseif ($form instanceof IfwZend_Form_DisplayGroup) {
                    $form->setAttrib('enctype', IfwZend_Form::ENCTYPE_MULTIPART);
                }
            }
        }
        $elementContent = implode($separator, $items);

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $elementContent . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $elementContent;
        }
    }
}
