<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_Validate_ToEmail extends IfwPsn_Vendor_Zend_Validate_NotEmpty
{
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Vendor_Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
        if (isset($context['to']) && $context['to'] != '') {
            return true;
        }

        return parent::isValid($value);
    }
}
