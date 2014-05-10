<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Option which will not be displayed on options page
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
require_once dirname(__FILE__) . '/../Field.php';

class IfwPsn_Wp_Options_Field_External extends IfwPsn_Wp_Options_Field
{
    public function render(array $params)
    {
        /**
         * @var IfwPsn_Wp_Options
         */
        $options = $params[0];

        $id = $options->getOptionRealId($this->_id);
        $name = $options->getPageId() . '['. $id .']';

        $html = '<input type="hidden" id="'. $id .'" name="'. $name .'" value="'. $options->getOption($this->_id) .'" />';

        echo $html;
    }
}
