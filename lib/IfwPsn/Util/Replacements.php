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
class IfwPsn_Util_Replacements
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
            $this->_replacements['default'] = $replacements;
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
     * @param string $group
     * @return $this
     */
    public function addPlaceholder($placeholder, $value = null, $group = 'default')
    {
        $this->_replacements[$group][$placeholder] = $value;
        return $this;
    }

    /**
     * @param $placeholder
     * @param $value
     * @param string $group
     * @return $this
     */
    public function setValue($placeholder, $value, $group = 'default')
    {
        $this->_replacements[$group][$placeholder] = $value;
        return $this;
    }

    /**
     * @param $placeholder
     * @param string $group
     * @return null
     */
    public function getValue($placeholder, $group = 'default')
    {
        if (isset($this->_replacements[$group][$placeholder])) {
            return $this->_replacements[$group][$placeholder];
        }
        return null;
    }

    /**
     * @param null $group
     * @return array
     */
    public function getPlaceholders($group = null)
    {
        return array_keys($this->getReplacements($group));
    }

    /**
     * @return array
     */
    public function getDefaultPlaceholders()
    {
        return array_keys($this->getReplacements('default'));
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
    protected function _getFlattenedReplacements()
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->_replacements));
        return iterator_to_array($iterator,true);
    }

    /**
     * @param null $group
     * @return array
     */
    public function getReplacements($group = null)
    {
        if ($group !== null && isset($this->_replacements[$group])) {
            $replacements = $this->_replacements[$group];
        } else {
            $replacements = $this->_getFlattenedReplacements();
        }

        if ($this->isAutoDelimiters()) {
            foreach($replacements as $k => $v) {
                $replacements[$this->_addDelimiters($k)] = $v;
                unset($replacements[$k]);
            }
        }

        return $replacements;
    }

    /**
     * @return array
     */
    public function getDefaultReplacements()
    {
        return $this->getReplacements('default');
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
