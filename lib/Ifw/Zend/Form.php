<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */ 
class Ifw_Zend_Form extends IfwZend_Form
{
    public function addElement($element, $name = null, $options = null)
    {
        if ($element instanceof IfwZend_Form_Element) {
            $name = $element->getName();
        }

        Ifw_Wp_Proxy::doAction($this->getName() . '_before_' . $name, $this);

        $result = parent::addElement($element, $name, $options);

        Ifw_Wp_Proxy::doAction($this->getName() . '_after_' . $name, $this);

        return $result;
    }
}
