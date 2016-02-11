<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\DataModelBuilder\Command;

use Blend\DataModelBuilder\Builder\Config\DefaultBuilderConfig;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CustomizedModelConfig extends DefaultBuilderConfig {

    public function getCustomizedRelationList() {
        return ['sys_order'];
    }

    public function getConverterForField($schema, $relation, $column, $dbtype, $fqcn) {

        if ($dbtype === 'timestamp without time zone') {
            return 'datetime_converter';
        }

        if (stripos($column, 'email') !== false) {
            return "email_field_converter";
        }

        return null;
    }

}
