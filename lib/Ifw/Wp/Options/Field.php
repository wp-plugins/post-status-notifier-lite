<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Options field
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
abstract class Ifw_Wp_Options_Field
{
    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_label;

    /**
     * @var string
     */
    protected $_description;



    /**
     * @param $id
     * @param $label
     */
    public function __construct($id, $label, $description = null)
    {
        $this->_id = $id;
        $this->_label = $label;
        if (!empty($description)) {
            $this->_description = $description;
        }
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->_label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param array $params
     * @return mixed
     */
    abstract public function render(array $params);
}
