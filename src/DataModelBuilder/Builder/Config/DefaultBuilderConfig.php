<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Builder\Config;

use Blend\Component\Exception\InvalidConfigException;
use Blend\DataModelBuilder\Builder\Config\BuilderConfig;
use Blend\Component\Database\Factory\Converter\DefaultFieldConverter;

/**
 * DefaultBuilderConfig is the default configuration that is used by
 * the DataModelCommand when no custom configuration is provided
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DefaultBuilderConfig extends BuilderConfig {

    public function getApplicationNamespace() {
        if (defined('BLEND_APPLICATION_NAMESPACE')) {
            return BLEND_APPLICATION_NAMESPACE;
        } else {
            throw new InvalidConfigException('The BLEND_APPLICATION_NAMESPACE is not defined');
        }
    }

    public function getModelRootNamespace() {
        return 'Database';
    }

    public function getSchemaListToGenerate() {
        return null; // will generate all schemas
    }

    public function getCustomizedRelationList() {
        return null; // nothing to customze
    }

    public function getLocalDateTimeFormat() {
        return [
            'date' => 'd-m-Y',
            'time' => 'H:i:s',
            'datetime' => 'd-m-Y H:i:s'
        ];
    }

    public function getConverterForField($schema, $relation, $column, $dbtype, $fqcn) {
        return null;
    }

    public function getFieldConverterClass() {
        return DefaultFieldConverter::class;
    }

}
