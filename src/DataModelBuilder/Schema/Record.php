<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Schema;

/**
 * Record is the base class for a record from the database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Record {

    protected $record;

    public function __construct(array $record) {
        $this->record = $this->normalize($record);
    }

    protected function normalize(array $record) {
        return $record;
    }

    protected function getString($name, $prettify = false, $replace = array()) {
        $name = $this->record[$name];
        if ($prettify) {
            return str_identifier(
                    str_replace(
                            array_keys($replace)
                            , array_values($replace)
                            , $name)
            );
        } else {
            return $name;
        }
    }

}
