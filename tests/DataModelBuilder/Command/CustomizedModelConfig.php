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
use Blend\Component\Database\Factory\Converter\DefaultFieldConverter as C;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CustomizedModelConfig extends DefaultBuilderConfig {

    public function getSchemaHelperListToGenerate() {
        return ['sys_user', 'aggrigations'];
    }

    public function getCustomizedRelationList() {
        return ['sys_order'];
    }

    protected function registerModelFactoryMethods() {
        $this->addModelFactoryMethod('sys_sample_view', 'secret_key', self::MODEL_FACTORY_RETURN_SINGLE);
        $this->addModelFactoryMethod('sys_sample_view', ['field1', 'generate_series'], self::MODEL_FACTORY_RETURN_MULTIPLE);
    }

    public function getConverterForField($schema, $relation, $column, $dbtype, $fqcn) {

        if (stripos($column, 'email') !== false) {
            return C::CONVERT_EMAIL_FIELD;
        }

        if (stripos($column, 'password') !== false) {
            return C::CONVERT_PASSWORD;
        }

        return null;
    }

}
