<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * URI Validator
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 * @package  Ifw_Wp
 */
class Ifw_Zend_Validate_Uri extends IfwZend_Validate_Abstract
{
    const MSG_URI = 'msgUri';
    
    protected $_messageTemplates = array(
        self::MSG_URI => 'Invalid URI',
    );

    /**
     * (non-PHPdoc)
     * @see IfwZend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        $this->_setValue($value);
    
        // Validate the URI
        $valid = IfwZend_Uri::check($value);
    
        if ($valid) {
            return true;
        } else {
            $this->_error(self::MSG_URI);
            return false;
        }
    }
}
