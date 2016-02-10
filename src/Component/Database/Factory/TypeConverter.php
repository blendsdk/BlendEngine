<?php


namespace Blend\Component\Database\Factory;

/**
 * TypeConverter is the base class for a PostgreSQL <=> PHP type converter
 * implementetion
 *
 * @author gevik
 */
abstract class TypeConverter {

    protected $converters = [];
    protected $cachedConverter = [];

    protected function addConverter($type, callable $converter) {
        $this->converters[$type] = $converter;
    }

    public function fromRecord(array &$record, $field, $type) {
        if (isset($this->cachedConverter[$field])) {
            $converters = $this->cachedConverter[$field];
        } else {
            $converters = [];
            foreach ([$type, $field] as $item) {
                if (isset($this->converters[$item])) {
                    $converters[] = $this->converters[$item];
                }
            }
            $this->cachedConverter[$field] = $converters;
        }
        foreach ($converters as $converter) {
            $record[$field] = call_user_func_array($converter, [$record[$field], $record]);
        }
    }

}
