<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id$
 * @package   
 */ 
class IfwPsn_Wp_Data_Importer 
{
    /**
     * @var string
     */
    protected $_file;

    /**
     * @var array (item_name_plural, item_name_singular)
     */
    protected $_xmlOptions = array();

    /**
     * @var string
     */
    protected $_error;


    /**
     * @param $file
     * @param $xmlOptions
     */
    public function __construct($file, $xmlOptions)
    {
        $this->_file = $file;
        if (is_array($xmlOptions)) {
            $this->_xmlOptions = $xmlOptions;
        }
    }

    /**
     * @param $modelname
     * @param $options
     * @return bool|int
     */
    public function import($modelname, $options = array())
    {
        if (empty($this->_file)) {
            $this->_error = __('Please select a valid import file.', 'ifw');
            return false;
        }

        $xml = simplexml_load_file($this->_file);

        // check for valid xml
        if (!$xml) {
            $this->_error = __('Please select a valid import file.', 'ifw');
            return false;
        }

        if (!isset($this->_xmlOptions['item_name_singular'])) {
            $this->_error = __('Missing item singular name.', 'ifw');
            return false;
        }

        $items = $this->_getItems($xml, $this->_xmlOptions['item_name_singular']);

        if (count($items) == 0) {
            $this->_error = __('No items found in import file.', 'ifw');
            return;
        }

        // import
        return IfwPsn_Wp_ORM_Model::import($modelname, $items, $options);
    }

    /**
     * @param $xml
     * @param $itemNodeName
     * @return array
     */
    protected function _getItems($xml, $itemNodeName, $itemNameCol = 'name')
    {
        $items = array();

        // check if xml contains items
        if (count($xml->{$itemNodeName}) == 0) {
            // no items found
            return $items;
        }

        foreach($xml->{$itemNodeName} as $item) {
            $tmpItem = array();
            foreach($item as $col) {
                $tmpItem[(string)$col[$itemNameCol]] = (string)$col;
            }
            array_push($items, $tmpItem);
        }

        return $items;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->_error;
    }
}
 