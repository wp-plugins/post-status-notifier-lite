<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_Validate_StatusTransition extends IfwZend_Validate_Abstract
{
    const MSG_INVALID_TRANSITION_ANYTHING_ALL = 'invalidTransitionAnythingAll';
    const MSG_INVALID_TRANSITION_COMBINATION = 'invalidTransitionCombination';

    protected $_messageTemplates = array(
        self::MSG_INVALID_TRANSITION_ANYTHING_ALL => 'a',
        self::MSG_INVALID_TRANSITION_COMBINATION => 'Invalid status combination',
    );

    public function __construct()
    {
        $this->_messageTemplates[self::MSG_INVALID_TRANSITION_ANYTHING_ALL] =
            __('Invalid status combination: before and after set to "anything" is not allowed', 'psn');
    }

    /**
     * (non-PHPdoc)
     * @see IfwZend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

        if ($context['status_before'] == 'anything' && $context['status_after'] == 'anything') {
            $this->_error(self::MSG_INVALID_TRANSITION_ANYTHING_ALL);
            return false;
        }

        return true;
    }
}
