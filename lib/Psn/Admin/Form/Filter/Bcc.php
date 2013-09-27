<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id$
 * @copyright   Copyright (c) 2012-2013 ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_Form_Filter_Bcc implements IfwZend_Filter_Interface
{
    protected $_isPremium = false;


    /**
     * @param $premium
     */
    public function __construct($premium = null)
    {
        if ($premium === true) {
            $this->_isPremium = true;
        }
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws IfwZend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $result = $value;

        if (!$this->_isPremium) {
            $result = '';
        }
        return $result;
    }

}
