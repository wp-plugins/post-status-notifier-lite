<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_Validate_ToEmail extends IfwZend_Validate_NotEmpty
{
    /**
     * (non-PHPdoc)
     * @see IfwZend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
        if ($context['recipient'] == 'individual_email') {
            return parent::isValid($value);
        }

        return true;
    }
}
