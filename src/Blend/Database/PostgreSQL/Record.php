<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database\PostgreSQL;

/**
 * Description of Record
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Record {

    protected $data;

    public function __construct($record = array()) {
        $this->data = $record;
    }

    protected function ucWords($string, $prefix = '', $postfix = '') {
        $str = str_replace(' ', '', ucwords(str_ireplace('id', 'ID', str_replace('_', ' ', $string))));
        return "{$prefix}{$str}{$postfix}";
    }

}
