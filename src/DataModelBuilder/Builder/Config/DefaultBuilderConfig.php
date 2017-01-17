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

use Blend\Component\Database\Factory\Converter\DefaultFieldConverter;
use Blend\Component\Exception\InvalidConfigException;

/**
 * DefaultBuilderConfig is the default configuration that is used by
 * the DataModelCommand when no custom configuration is provided.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DefaultBuilderConfig extends BuilderConfig
{
    /**
     * Should return the root namespace of your application.
     */
    public function getApplicationNamespace()
    {
        /*
         * This value normally is set the in the bin/[appname].php
         */
        if (defined('BLEND_APPLICATION_NAMESPACE')) {
            return BLEND_APPLICATION_NAMESPACE;
        } else {
            throw new InvalidConfigException('The BLEND_APPLICATION_NAMESPACE is not defined');
        }
    }

    /**
     * Should resturn the root namespace of your DAL (Data Access Layer)
     * "Database" for example.
     */
    public function getModelRootNamespace()
    {
        return 'Database';
    }

    /**
     * Should return a string array of schemas to generate otherwise it should
     * return "null" to generate all the schemas.
     */
    public function getSchemaListToGenerate()
    {
        return null; // will generate all schemas
    }

    /**
     * Should return a string array of relation (tables and views) names that
     * you are going to customize.
     */
    public function getCustomizedRelationList()
    {
        return null; // nothing to customze
    }

    /**
     * Should return the local date format, for example
     * return [
     *      'date' => 'd-m-Y',
     *      'time' => 'H:i:s',
     *      'datetime' => 'd-m-Y H:i:s'
     * ].
     */
    public function getLocalDateTimeFormat()
    {
        return [
            'date' => 'd-m-Y',
            'time' => 'H:i:s',
            'datetime' => 'd-m-Y H:i:s',
        ];
    }

    /**
     * Should return a converter identifier based on the fully qualified
     * column name, thatis schema.relation.column (public.userser.user_name).
     * @param mixed $schema
     * @param mixed $relation
     * @param mixed $column
     * @param mixed $dbtype
     * @param mixed $fqcn
     */
    public function getConverterForField($schema, $relation, $column, $dbtype, $fqcn)
    {
        return null;
    }

    /**
     * Should return a FQCN string of your FieldConverter class.
     */
    public function getFieldConverterClass()
    {
        return DefaultFieldConverter::class;
    }

    /**
     * Should return either a null meaning all or an array with FQRN of the
     * relations for which a Schema object needs to be generated.
     */
    public function getSchemaHelperListToGenerate()
    {
        return null;
    }
}
