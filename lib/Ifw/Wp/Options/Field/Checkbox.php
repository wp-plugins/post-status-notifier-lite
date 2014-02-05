<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options field checkbox
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Options_Field_Checkbox extends Ifw_Wp_Options_Field
{
    public function render(array $params)
    {
        /**
         * @var Ifw_Wp_Options
         */
        $options = $params[0];

        $id = $options->getOptionRealId($this->_id);
        $name = $options->getPageId() . '['. $id .']';

        $html = '<input type="checkbox" id="'. $id .'" name="'. $name .'" value="1" ' . checked(1, $options->getOption($this->_id), false) . '/>';
        if (!empty($this->_description)) {
            $html .= '<label for="'. $id .'"> '  . $this->_description . '</label>';
        }
        echo $html;
    }
}
