<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Free text
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
require_once dirname(__FILE__) . '/../Field.php';

class IfwPsn_Wp_Options_Field_Html extends IfwPsn_Wp_Options_Field
{
    public function render(array $params)
    {
        echo $this->_description;
    }
}
