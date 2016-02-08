<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Command;

use Blend\Component\Exception\InvalidConfigException;
use Blend\DataModelBuilder\Command\ModelBuilderConfig;

/**
 * ModelBuilderDefaultConfig is the default configuration that is used by
 * the DataModelCommand when no custom configuration is provided
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ModelBuilderDefaultConfig extends ModelBuilderConfig {

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

}
