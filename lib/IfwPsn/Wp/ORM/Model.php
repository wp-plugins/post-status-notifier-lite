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
class IfwPsn_Wp_ORM_Model extends IfwPsn_Wp_ORM_ModelParis
{
    /**
     * Checks if table exists
     * @return bool
     */
    public function exists()
    {
        global $wpdb, $table_prefix;
        $result = false;

        $r = new ReflectionProperty($this, '_table');
        $query = sprintf('SHOW TABLES LIKE "%s"', $table_prefix . $r->getValue());
        if ($wpdb->get_row($query) !== null) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param $modelName
     * @param $id
     * @param array $options
     * @return bool
     */
    public static function duplicate($modelName, $id, $options = array())
    {
        if (isset($options['name_format'])) {
            $newNameFormat = $options['name_format'];
        } else {
            $newNameFormat = '%s [%s%s]';
        }
        if (isset($options['name_col'])) {
            $nameCol = $options['name_col'];
        } else {
            $nameCol = 'name';
        }
        if (isset($options['id_col'])) {
            $idCol = $options['id_col'];
        } else {
            $idCol = 'id';
        }


        $item = self::factory($modelName)->find_one((int)$id);
        $values = $item->as_array();

        unset($values[$idCol]);

        // if a values callback is set, call it
        if (isset($options['values_callback']) && is_callable($options['values_callback'])) {
            $values = call_user_func($options['values_callback'], $values);
        }

        $count = self::factory($modelName)->where_like($nameCol, sprintf($newNameFormat, $values[$nameCol], __('Duplicate', 'ifw'), '%') . '%')->count();

        $copyCount = '';
        if ($count > 0) {
            $copyCount = $count + 1;
        }
        $values[$nameCol] = sprintf($newNameFormat, $values[$nameCol], __('Duplicate', 'ifw'), $copyCount);

        return IfwPsn_Wp_ORM_Model::factory($modelName)->create($values)->save();
    }

    /**
     * @param $modelName
     * @param $items
     * @param array $options
     * @return int
     */
    public static function import($modelName, $items, $options = array())
    {
        $counter = 0;

        if (!is_array($items)) {
            $items = array($items);
        }

        if (isset($options['id_col'])) {
            $idCol = $options['id_col'];
        } else {
            $idCol = 'id';
        }
        if (isset($options['name_col'])) {
            $nameCol = $options['name_col'];
        } else {
            $nameCol = 'name';
        }
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'];
        }

        // create imported items
        foreach ($items as $item) {

            unset($item[$idCol]);

            if (!empty($prefix)) {
                $item[$nameCol] = $prefix . $item[$nameCol];
            }
            // if a items callback is set, call it
            if (isset($options['item_callback']) && is_callable($options['item_callback'])) {
                $item = call_user_func($options['item_callback'], $item);
            }

            if (IfwPsn_Wp_ORM_Model::factory($modelName)->create($item)->save() != false) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * @param $modelName
     * @param $itemList
     * @param array $options
     */
    public static function export($modelName, $itemList, $options = array())
    {
        if (!is_array($itemList)) {
            $itemList = array($itemList);
        }
        $itemList = array_map('intval', $itemList);

        $items = IfwPsn_Wp_ORM_Model::factory($modelName)->where_in('id', $itemList)->find_array();

        if (isset($options['item_name_plural'])) {
            $itemNamePlural = $options['item_name_plural'];
        } else {
            $itemNamePlural = 'items';
        }
        if (isset($options['item_name_singular'])) {
            $itemNameSingular = $options['item_name_singular'];
        } else {
            $itemNameSingular = 'item';
        }
        if (isset($options['filename'])) {
            $filename = $options['filename'];
        } else {
            $filename = 'Export_'. date('Y-m-d_H_i_s');
        }

        $result = "<$itemNamePlural>\n";

        foreach ( $items as $item ) {

            if (isset($options['item_callback']) && is_callable($options['item_callback'])) {
                $item = call_user_func($options['item_callback'], $item);
            }

            $result .= "<$itemNameSingular>\n";
            foreach ($item as $field => $value) {

                if (isset($options['value_callback']) && is_callable($options['value_callback'])) {
                    $value = call_user_func($options['value_callback'], $value);
                } else {
                    $value = '<![CDATA['. $value . ']]>';
                }

                $result .= "\t" . '<column name="'. $field .'">'. $value .'</column>' . "\n";
            }
            $result .= "</$itemNameSingular>\n";
        }
        $result .= "</$itemNamePlural>\n";

        $xml = new SimpleXMLElement($result);

        $filename .= '.xml';

        // so far export means to download an xml file, so this is directly integrated here.
        // refactoring could return the xml and pass it to an export handler
        header('Content-disposition: attachment; filename="'. $filename .'"');
        header('Content-type: "text/xml"; charset="utf8"');
        echo $xml->asXML();
        exit;
    }

}
