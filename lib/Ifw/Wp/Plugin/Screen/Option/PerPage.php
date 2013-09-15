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
class Ifw_Wp_Plugin_Screen_Option_PerPage extends Ifw_Wp_Plugin_Screen_Option_Abstract
{
    /**
     * @var string
     */
    protected $_label;

    /**
     * @var string
     */
    protected $_optionName;

    /**
     * @var string
     */
    protected $_default;

    /**
     * @param null $label
     * @param null $optionName
     * @param null $default
     */
    public function __construct(Ifw_Wp_Plugin_Manager $pm, $label = null, $optionName = null, $default = null)
    {
        if ($label !== null) {
            $this->_label = $label;
        }
        if ($optionName !== null) {
            $this->_optionName = $optionName;
        }
        if ($default !== null) {
            $this->_default = $default;
        }

        parent::__construct($pm);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return 'per_page';
    }

    /**
     * Registers the option
     * @throws Ifw_Wp_Plugin_Screen_Exception
     */
    protected function _register()
    {
        $label = $this->getLabel();
        if (empty($label)) {
            throw new Ifw_Wp_Plugin_Screen_Exception('Missing screen option label');
        }
        $optionName = $this->getOptionName();
        if (empty($optionName)) {
            throw new Ifw_Wp_Plugin_Screen_Exception('Missing screen option name');
        }

        if ($this->getDefault() !== null) {
            Ifw_Wp_Proxy_Screen::addOptionPerPage($label, $optionName, $this->getDefault());
        } else {
            Ifw_Wp_Proxy_Screen::addOptionPerPage($label, $optionName);
        }
    }

    /**
     * @param $value
     * @return null
     */
    protected function _getOptionCallback($value)
    {
        if (empty($value) || $value < 1) {
            $value = Ifw_Wp_Proxy_Screen::getOption('per_page', 'default');
        }
        return (int)$value;
    }

    public function setDefault($default)
    {
        $this->_default = $default;
    }

    public function getDefault()
    {
        return $this->_default;
    }

    public function setLabel($label)
    {
        $this->_label = $label;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function setOptionName($optionName)
    {
        $this->_optionName = $optionName;
    }

    public function getOptionName()
    {
        return $this->_optionName;
    }

}
