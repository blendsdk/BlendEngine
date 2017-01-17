<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory\Converter;

use Blend\Component\Exception\InvalidConfigException;

if (!defined('FIELD_CONVERT_TO_DB')) {
    define('FIELD_CONVERT_TO_DB', 1);
}

if (!defined('FIELD_CONVERT_TO_MODEL')) {
    define('FIELD_CONVERT_TO_MODEL', 2);
}

/**
 * FieldConverter is the base class for a PostgreSQL <=> PHP data converter
 * implementetion.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class FieldConverter
{
    protected $converters = array();
    protected $cachedConverter = array();
    protected $options = array();

    const DIR_FROM_RECORD = 'toModel';
    const DIR_FROM_MODEL = 'toDbRecord';

    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    protected function addConverter($type, IConverter $converter)
    {
        $this->converters[$type] = $converter;
    }

    public function fromRecord(array &$record, $field, $type)
    {
        return $this->convert($record, $field, $type, self::DIR_FROM_RECORD);
    }

    public function fromModel(array &$record, $field, $type)
    {
        return $this->convert($record, $field, $type, self::DIR_FROM_MODEL);
    }

    public function convert(array &$record, $field, $type, $direction)
    {
        if (array_key_exists($field, $record)) {
            if (isset($this->cachedConverter[$field])) {
                $converters = $this->cachedConverter[$field];
            } else {
                $converters = array();
                foreach (array($type, $field) as $item) {
                    if (isset($this->converters[$item])) {
                        $converters[] = $this->converters[$item];
                    }
                }
                if (count($converters) !== 0) {
                    $this->cachedConverter[$field] = $converters;
                } else {
                    throw new InvalidConfigException("Unable to find field converter [{$type}] for [{$field}]!");
                }
            }
            foreach ($converters as $converter) {
                $record[$field] = call_user_func(array($converter, $direction), $record[$field]);
            }
        }

        return $record;
    }
}
