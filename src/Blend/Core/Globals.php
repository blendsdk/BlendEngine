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
    function array_reindex(array $array, callable $indexer) {
        $result = array();
        foreach ($array as $item) {
            $key = call_user_func($indexer, $item);
            if (!empty($key)) {
                $result[$key][] = $item;
            }
        }
        return $result;
    }

}
