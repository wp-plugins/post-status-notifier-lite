<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options field password
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Options_Field_Password extends Ifw_Wp_Options_Field
{
    public function render(array $params)
    {
        /**
         * @var Ifw_Wp_Options
         */
        $options = $params[0];

        $id = $options->getOptionRealId($this->_id);
        $name = $options->getPageId() . '['. $id .']';

        $html = '<input type="password" autocomplete="off" id="'. $id .'" name="'. $name .'" value="'. $options->getOption($this->_id) .'" />';
        if (!empty($this->_description)) {
            $html .= '<br><label for="'. $id .'"> '  . $this->_description . '</label>';
        }
        echo $html;
    }
}
