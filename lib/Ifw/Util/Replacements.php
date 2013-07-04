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
class Ifw_Util_Replacements
{
    /**
     * @var string
     */
    protected $_delimiterFront = '[';

    /**
     * @var string
     */
    protected $_delimiterBehind = ']';

    /**
     * @var bool
     */
    protected $_autoDelimiters = false;

    /**
     * @var array
     */
    protected $_replacements = array();



    /**
     * @param null $replacements
     * @param array $options
     */
    public function __construct($replacements = null, $options = array())
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
        if (is_array($replacements)) {
            $this->_replacements = $replacements;
        }
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['delimiter_front']) && !empty($options['delimiter_front'])) {
            $this->_delimiterFront = $options['delimiter_front'];
        }
        if (isset($options['delimiter_behind']) && !empty($options['delimiter_behind'])) {
            $this->_delimiterBehind = $options['delimiter_behind'];
        }
        if (isset($options['auto_delimiters']) && $options['auto_delimiters'] === true) {
            $this->_autoDelimiters = true;
        }
    }

    /**
     * @param $placeholder
     * @param null $value
     * @return $this
     */
    public function addPlaceholder($placeholder, $value = null)
    {
        $this->_replacements[$placeholder] = $value;
        return $this;
    }

    /**
     * @param $placeholder
     * @param $value
     * @return $this
     */
    public function setValue($placeholder, $value)
    {
        $this->_replacements[$placeholder] = $value;
        return $this;
    }

    /**
     * @param $placeholder
     * @return null
     */
    public function getValue($placeholder)
    {
        if (isset($this->_replacements[$placeholder])) {
            return $this->_replacements[$placeholder];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        return array_keys($this->getReplacements());
    }

    /**
     * @return bool
     */
    public function isAutoDelimiters()
    {
        return $this->_autoDelimiters;
    }

    /**
     * @param $placeholder
     * @return string
     */
    protected function _addDelimiters($placeholder)
    {
        return $this->_delimiterFront . $placeholder . $this->_delimiterBehind;
    }

    /**
     * @return array
     */
    public function getReplacements()
    {
        $replacements = array();

        if ($this->isAutoDelimiters()) {
            foreach($this->_replacements as $k => $v) {
                $replacements[$this->_addDelimiters($k)] = $v;
            }
        } else {
            $replacements = $this->_replacements;
        }

        return $replacements;
    }

    /**
     * @param $string
     * @return string
     */
    public function replace($string)
    {
        return strtr($string, $this->getReplacements());
    }
}
