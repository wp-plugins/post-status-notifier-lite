<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2012-2013 ifeelweb.de
 * @version   $Id$
 * @package   
 */ 
class Ifw_Wp_Options_Manager 
{
    /**
     * @var Ifw_Wp_Plugin_Manager
     */
    private $_pm;

    /**
     * @var array
     */
    private $_generalOptions = array();

    /**
     * @var array
     */
    private $_externalOptions = array();



    public function __construct(Ifw_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
        $this->_init();
    }

    protected function _init()
    {
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_general_options_init', array($this, 'registerOptionsCallback'));
        Ifw_Wp_Proxy_Action::add($this->_pm->getAbbrLower() . '_external_options_init', array($this, 'registerOptionsCallback'));
    }

    /**
     * @param Ifw_Wp_Options_Field $option
     */
    public function addGeneralOption(Ifw_Wp_Options_Field $option)
    {
        array_push($this->_generalOptions, $option);
    }

    /**
     * @param string $id
     */
    public function registerExternalOption($id)
    {
        array_push($this->_externalOptions, new Ifw_Wp_Options_Field_External($id, ''));
    }

    /**
     * @param Ifw_Wp_Options_Section $section
     */
    public function registerOptionsCallback(Ifw_Wp_Options_Section $section)
    {
        switch ($section->getId()) {
            case 'general':
                $options = $this->_generalOptions;
                break;
            case 'external':
                $options = $this->_externalOptions;
                break;
        }

        foreach($options as $option) {
            $section->addField($option);
        }
    }

    /**
     * @param $id
     * @param $value
     */
    public function updateOption($id, $value)
    {
        $options = Ifw_Wp_Proxy::getOption($this->_pm->getOptions()->getPageId());
        $options[$this->_pm->getOptions()->getOptionRealId($id)] = $value;
        Ifw_Wp_Proxy::updateOption($this->_pm->getOptions()->getPageId(), $options);
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasOption($id)
    {
        $options = Ifw_Wp_Proxy::getOption($this->_pm->getOptions()->getPageId());
        return isset($options[$this->_pm->getOptions()->getOptionRealId($id)]);
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function getOption($id)
    {
        if ($this->hasOption($id)) {
            $options = Ifw_Wp_Proxy::getOption($this->_pm->getOptions()->getPageId());
            return $options[$this->_pm->getOptions()->getOptionRealId($id)];
        }

        return null;
    }
}
