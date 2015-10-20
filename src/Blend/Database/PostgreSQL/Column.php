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

use Blend\Database\PostgreSQL\Record;

/**
 * Description of Column
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Column extends Record {

    public function getDescription() {
        return $this->data['description'] = 'Column is '
                . ($this->data['is_nullable'] ? 'Nullable' : 'Not Nullable')
                . '. Defaults to '
                . (empty($this->data['column_default']) ? 'NULL' : $this->data['column_default']);
    }

    public function getDataType() {
        return str_replace(' ', '_', $this->data['data_type']);
    }

    public function getColumnFunctionName($prefix) {
        return $prefix . $this->ucWords($this->getColumnName());
    }

    public function getColumnName($uppercase = false) {
        if ($uppercase) {
            return strtoupper($this->data['column_name']);
        } else {
            return $this->data['column_name'];
        }
    }

}
