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
     * @return type
     */
    function array_remove_nulls(array $array) {
        return array_filter($array, function($v, $k) {
            return $v !== null;
        }, ARRAY_FILTER_USE_BOTH);
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