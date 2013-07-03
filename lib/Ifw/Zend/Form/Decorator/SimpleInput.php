<?php

class Ifw_Zend_Form_Decorator_SimpleInput extends IfwZend_Form_Decorator_Abstract
{
    protected $_formatText = '<label for="%s">%s</label><input id="%s" name="%s" type="text" value="%s" %s />';
    protected $_formatTextarea = '<label for="%s">%s</label><textarea id="%s" name="%s" cols="%s" rows="%s">%s</textarea>';
    protected $_formatSelect = '<label for="%s">%s</label><select id="%s" name="%s" />%s</select>';
    protected $_formatCheckbox = '<label for="%s">%s</label><input id="%s" name="%s" type="checkbox" value="%s" %s />';

    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = $element->getLabel();
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue(), ENT_COMPAT, Ifw_Wp_Proxy_Blog::getCharset());

        switch ($element->getType()) {

            case 'IfwZend_Form_Element_Textarea':
                $value = html_entity_decode($element->getValue(), ENT_COMPAT, Ifw_Wp_Proxy_Blog::getCharset());
                $cols = $element->getAttrib('cols');
                $rows = $element->getAttrib('rows');
                $markup = sprintf($this->_formatTextarea, $name, $label, $id, $name, $cols, $rows, $value);
                break;

            case 'IfwZend_Form_Element_Select':

                $options = '';
                foreach($element->getAttrib('options') as $k => $v) {
                    $options .= sprintf('<option value="%s"%s>%s</option>',
                        $k,
                        $k == $value ? ' selected="selected"' : '',
                        $v);
                }
                $markup = sprintf($this->_formatSelect, $id, $label, $id, $name, $options);
                break;

            case 'IfwZend_Form_Element_Checkbox':

                $value = $element->getCheckedValue();

                $checked = $element->isChecked() ? 'checked="checked"' : '';
                $markup = sprintf($this->_formatCheckbox, $id, $label, $id, $name, $value, $checked);
                break;

            case 'IfwZend_Form_Element_Text':
            default:
                $additionalParams = '';
                if ($element->getAttrib('maxlength') != null) {
                    $additionalParams .= sprintf('maxlength="%s"', $element->getAttrib('maxlength'));
                }
                $markup  = sprintf($this->_formatText, $id, $label, $id, $name, $value, $additionalParams);
                break;
        }

        return $markup;
    }
    
}
