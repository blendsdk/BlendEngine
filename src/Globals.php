<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * These functions are available globally
 */
if (!function_exists('str_replace_template')) {

    /**
     * Replace all occurrences of the associative array keys with
     * the associative array values
     * @param string $template
     * @param mixed $context
     * @return string
     */
    function str_replace_template($template, $context = array()) {


        if (is_array($context)) {
            return str_replace(array_keys($context), array_values($context), $template);
        } else {
            return $template;
        }
    }

}

if (!function_exists('array_reindex')) {

    /**
     * Creates a new array indexed by the result of calling a closure
     * @param array $array
     * @param callable $indexer
     * @return array
     */
    function array_reindex(array $array, callable $indexer, $single = false) {
        $result = array();
        foreach ($array as $item) {
            $key = call_user_func($indexer, $item);
            if (!empty($key)) {
                if (!$single) {
                    $result[$key][] = $item;
                } else {
                    $result[$key] = $item;
                }
            }
        }
        return $result;
    }

}

if (!function_exists('array_remove_nulls')) {

    /**
     * Removes the null elements from an array
     * @param array $array
     * @return array
     */
    function array_remove_nulls(array $array) {
        return array_filter($array, function($v, $k) {
            return $v !== null;
        }, ARRAY_FILTER_USE_BOTH);
    }

}

if (!function_exists('sql_join')) {

    /**
     * Shorthand helper to create a SQL JOIN condition
     * @param string $left The condition to the left
     * @param string $right The condition to the right
     * @param string $type The condition operator, defaults to '='
     * @return array
     */
    function sql_join($left, $right, $type = '=') {
        return array($type, $left, $right);
    }

}


if (!function_exists('sqlstr')) {

    /**
     * Wrapper function around the Blend\Component\Database\SQL\SQLString class
     * @param syting $str Passed to the SQLString's constructor
     * @return SQLString
     */
    function sqlstr($str) {
        return new Blend\Component\Database\SQL\SQLString($str);
    }

}

if (!function_exists('render_php_template')) {

    /**
     * Renders a PHP template (view) and retusn the results either as string
     * or writes the contents to a file
     * @param string $templateFile The template (view) to render
     * @param array $context An key/valye array pass to the template
     * @param string $outputFile Optional filename to write the results to
     * @param boolen $trim Whether to trim the result, defaults to true
     * @return string The result as string
     */
    function render_php_template($templateFile, $context, $outputFile = null, $trim = true) {
        if (is_array($context)) {
            extract($context, EXTR_PREFIX_SAME, 'data');
        }
        ob_start();
        ob_implicit_flush(false);
        require($templateFile);
        $result = ($trim === true ? trim(ob_get_clean()) : ob_get_clean());
        if (!is_null($outputFile)) {
            file_put_contents($outputFile, $result);
        }
        return $result;
    }

}

if (!function_exists('str_identifier')) {

    /**
     * A wrapper around wcwords to create an identifier ike string.
     * In addition it replaces "_" with spaces so the ucwords can recongnize
     * the phrase as separate words
     * @param string $string The string to parse
     * @param string $prefix Optional prefix
     * @param string $postfix Optional postfix
     * @return string The result
     */
    function str_identifier($string, $prefix = '', $postfix = '') {
        return $prefix . str_replace(' ', '', ucwords(str_replace('_', ' ', $string))) . $postfix;
    }

}

if (!function_exists('print_php_header')) {

    /**
     * Helper function for printing the php file header (<?php)
     * This function is onternally used for template generation
     */
    function print_php_header() {
        echo "<?php\n";
    }

}

if (!function_exists('is_closure')) {

    /**
     * Check if a given object is a Closure
     * @param mixed $obj
     * @return boolean
     */
    function is_closure($obj) {
        return is_object($obj) && ($obj instanceof Closure);
    }

}

if (!function_exists('is_array_assoc')) {

    /**
     * Check if the given array is an associative array
     * @param type $arr
     */
    function is_array_assoc($arr) {
        if (is_array($arr) && count($arr) !== 0) {
            return array_keys($arr) !== range(0, count($arr) - 1);
        } else {
            return false;
        }
    }

}

if (!function_exists('array_get_key_value')) {

    /**
     * Gets a value from an array using a key, and returns the $default
     * if the key does not exist
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get_key_value(array $array, $key, $default = null) {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $default;
        }
    }

}