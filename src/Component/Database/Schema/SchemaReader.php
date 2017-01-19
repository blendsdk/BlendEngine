<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database\Schema;

use Blend\Component\Database\Database;
use Blend\Component\Database\SQL\SQLString;
use Blend\Component\Database\SQL\Statement\SelectStatement;

/**
 * The SchemaReader can be used to read a PostgreSQL database schema
 * to be processed further by a code generator.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaReader
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Reads the schemas from the database.
     *
     * @return Schema
     */
    public function read()
    {
        return $this->readSchemas();
    }

    /**
     * Read the Schemas from the database.
     *
     * @return \Blend\Component\Database\Schema\Schema
     */
    private function readSchemas()
    {
        $skip = array('pg_toast', 'pg_temp_1', 'pg_catalog', 'pg_toast_temp_1', 'information_schema');
        $sql = new SelectStatement();
        $sql->from('information_schema.schemata')
                ->selectAll()
                ->where(sqlstr('schema_name')->notInList($skip, SQLString::STRING_RENDERER()));

        $result = array();
        $list = $this->database->executeQuery($sql);
        foreach ($list as $record) {
            $schema = new Schema($record);
            $result[$schema->getName()] = $schema;
            $this->readRelations($schema);
        }

        return $result;
    }

    /**
     * Reads relations of a Schema.
     *
     * @param \Blend\Component\Database\Schema\Schema $schema
     */
    private function readRelations(Schema $schema)
    {
        $sql = new SelectStatement();
        $sql->from('information_schema.tables')
                ->selectAll()
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'));
        $params = array(':table_schema' => $schema->getName());
        foreach ($this->database->executeQuery($sql, $params) as $record) {
            $relation = new Relation($record, $schema);
            $schema->addRelation($relation);
            $this->readColumns($relation);
            $this->readConstraints($relation);
        }
    }

    /**
     * Read columns of a Relation.
     *
     * @param \Blend\Component\Database\Schema\Relation $relation
     */
    protected function readColumns(Relation $relation)
    {
        $sql = new SelectStatement();
        $sql->from('information_schema.columns')
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('table_name')->equalsTo(':table_name'));

        $params = array(':table_schema' => $relation->getSchema()->getName(), ':table_name' => $relation->getName());
        foreach ($this->database->executeQuery($sql, $params) as $record) {
            $column = new Column($record, $relation);
            $relation->addColumn($column);
        }
    }

    /**
     * Reads the constrains of a relation.
     *
     * @param \Blend\Component\Database\Schema\Relation $relation
     */
    protected function readConstraints(Relation $relation)
    {
        $constraint_type = array('UNIQUE', 'PRIMARY KEY', 'FOREIGN KEY');
        $tableConstQuery = new SelectStatement();
        $tableConstQuery->from('information_schema.table_constraints')
                ->where(sqlstr('constraint_type')->inList($constraint_type, SQLString::STRING_RENDERER()))
                ->andWhere(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('table_name')->equalsTo(':table_name'));

        $tableConstQueryParams = array(
            ':table_schema' => $relation->getSchema()->getName(),
            ':table_name' => $relation->getName(),
        );

        $constColumnQuery = new SelectStatement();
        $constColumnQuery->from('information_schema.constraint_column_usage')
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('constraint_name')->equalsTo(':constraint_name'));

        foreach ($this->database->executeQuery($tableConstQuery, $tableConstQueryParams) as $tableConst) {
            $constColumnParams = array(
                ':table_schema' => $tableConst['table_schema'],
                ':constraint_name' => $tableConst['constraint_name'],
            );

            $constColumns = $this->database->executeQuery($constColumnQuery, $constColumnParams);

            $constraint = new Constraint($tableConst);
            foreach ($constColumns as $constColumn) {
                $constraint->addColumn($relation->getColumn($constColumn['column_name']));
            }
            $relation->addConstraint($constraint);
        }
    }
}
