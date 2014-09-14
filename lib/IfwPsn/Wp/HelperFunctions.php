<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Helper functions
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */

if (!function_exists('ifw_debug')) {

    /**
     * Writes debug info to debug.log
     *
     * @param $var
     * @param bool $backtrace
     */
    function ifw_debug ($var, $backtrace = false, $verbose = true) {

        if (WP_DEBUG === true) {

            $bt = debug_backtrace();
            $pathinfo = pathinfo($bt[0]['file']);

            $output = '';
            if ($verbose) {
                $output .= __FUNCTION__ . ' in ';
                $output .= $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['basename'] . ':' . $bt[0]['line'] . ':' .
                    ' ('. gettype($var) . ') ';
            }

            if (is_array($var) || is_object($var)) {
                $output .= print_r($var, true);
            } elseif (is_bool($var)) {
                $output .= var_export($var, true);
            } else {
                $output .= $var;
            }
            error_log($output);

            if ($backtrace) {
                $backtrace = array_reverse(debug_backtrace());

                $backtrace_output = '';

                $counter = 0;

                foreach ($backtrace as $row) {
                    if ((count($backtrace)-1) == $counter) {
                        break;
                    }

                    $file = (isset($row['file'])) ? $row['file'] : '';
                    $line = (isset($row['line'])) ? $row['line'] : '';
                    $class = (isset($row['class'])) ? $row['class'] : '';
                    $function = (isset($row['function'])) ? $row['function'] : '';

                    $backtrace_output .= $counter .': '. $file .':'. $line .
                        ', class: '. $class .', function: '. $function . PHP_EOL;
                    $counter++;
                }
                error_log(__FUNCTION__ . ' backtrace:' . PHP_EOL . $backtrace_output);
            }
        }
    }
}

if (!function_exists('ifw_log_error')) {

    /**
     * Writes error message to debug.log
     * @param $error
     */
    function ifw_log_error ($error) {

        if (WP_DEBUG === true) {
            error_log($error);
        }
    }
}

if (!function_exists('ifw_unserialize_recursive')) {

    /**
     * @param $data
     * @return mixed|string
     */
    function ifw_unserialize_recursive ($data) {

        if (is_serialized($data)) {

            $data = trim($data);
            $result = unserialize($data);

            if (is_array($result)) {
                foreach($result as &$r) $r = ifw_unserialize_recursive($r);
            }
            return $result;

        } elseif (is_array($data)) {

            foreach ($data as &$r) {
                $r = ifw_unserialize_recursive($r);
            }
            return $data;

        } else {
            return $data;
        }
    }
}

if (!function_exists('ifw_array_search_recursive_key')) {

    /**
     * @param array $array
     * @param $key
     * @return null
     */
    function ifw_array_search_recursive_key (array $array, $key) {

        $iterator  = new RecursiveArrayIterator($array);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive as $k => $value) {
            if ($key === $k) {
                return $value;
            }
        }

        return null;
    }
}

if (!function_exists('ifw_rrmdir')) {

    /**
     * @param $dir
     */
    function ifw_rrmdir ($dir) {

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}