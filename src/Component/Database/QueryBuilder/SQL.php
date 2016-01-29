<?php

namespace Blend\Component\Database\QueryBuilder;

class SQL {

    public static function castField($field, $type = 'text') {
        return self::cast(self::field($field), $type);
    }

    public static function cast($value, $type = 'text') {
        return "{$value}::{$type}";
    }

    protected static function tableAlias($tablename) {
        if (is_array($tablename)) {
            $keys = array_keys($tablename);
            $alias = $keys[0];
            return "{$tablename[$alias]} $alias";
        } else {
            return $tablename;
        }
    }

    public static function field($field) {
        if (is_array($field)) {
            $keys = array_keys($field);
            $alias = $keys[0];
            return "{$alias}.{$field[$alias]}";
        } else {
            return $field;
        }
    }

}
