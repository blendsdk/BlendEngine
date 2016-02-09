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

use Blend\Component\Database\Database;
use Blend\Component\Database\SQL\Statement\SelectStatement;
use Blend\Component\Database\SQL\SQLString;
use Blend\DataModelBuilder\Schema\Schema;
use Blend\DataModelBuilder\Schema\Column;
use Blend\DataModelBuilder\Schema\Relation;

/**
 * Read the database schema from a PostgreSQL Database for code 
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaReader {

    /**
     * Instance of a Database
     * @var Database 
     */
    protected $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Laods the database schemas with their tables, column and contraints
     * @return Schema[] Array of Schema objects
     */
    public function load() {
        return $this->getSchemas();
    }

    /**
     * Load the schemas from the database
     * @return Schema[]
     */
    protected function getSchemas() {
        $skip = ['pg_toast', 'pg_temp_1', 'pg_catalog', 'pg_toast_temp_1', 'information_schema'];
        $sql = new SelectStatement();
        $sql->from('information_schema.schemata')
                ->selectAll()
                ->where(sqlstr('schema_name')->notInList($skip, SQLString::STRING_RENDERER()));

        $result = [];
        $list = $this->database->executeQuery($sql);
        $singleSchema = count($list) === 1;
        foreach ($list as $record) {
            $record['is_single'] = $singleSchema;
            $schema = new Schema($record);
            $this->loadRelationsForSchema($schema);
            $result[$schema->getName()] = $schema;
        }
        return $result;
    }

    /**
     * Loads the Relations fro a given schema
     * @param Schema $schema
     */
    protected function loadRelationsForSchema(Schema $schema) {
        $sql = new SelectStatement();
        $sql->from('information_schema.tables')
                ->selectAll()
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'));
        $params = [':table_schema' => $schema->getName()];
        foreach ($this->database->executeQuery($sql, $params) as $record) {
            $relation = new Relation($record);
            $schema->addRelation($relation);
            $this->loadColumnsForRelation($relation);
            $this->loadContraintsForRelation($relation);
        }
    }

    /**
     * Loads the contains for a given Relation
     * @param Relation $relation
     */
    protected function loadContraintsForRelation(Relation $relation) {
        "select * from information_schema.table_constraints where constraint_type in ('UNIQUE','PRIMARY KEY','FOREIGN KEY') and table_schema = :table_schema and table_catalog = :table_catalog and table_name = :table_name";

        $constraint_type = ['UNIQUE', 'PRIMARY KEY', 'FOREIGN KEY'];
        $tableConstQuery = new SelectStatement();
        $tableConstQuery->from('information_schema.table_constraints')
                ->where(sqlstr('constraint_type')->inList($constraint_type, SQLString::STRING_RENDERER()))
                ->andWhere(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('table_name')->equalsTo(':table_name'));

        $tableConstQueryParams = array(
            ':table_schema' => $relation->getSchemaName(),
            ':table_name' => $relation->getName()
        );

        $constColumnQuery = new SelectStatement();
        $constColumnQuery->from('information_schema.constraint_column_usage')
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('table_name')->equalsTo(':table_name'))
                ->andWhere(sqlstr('constraint_name')->equalsTo(':constraint_name'));

        foreach ($this->database->executeQuery($tableConstQuery, $tableConstQueryParams) as $tableConst) {
            $constColumnParams = array(
                ':table_schema' => $tableConst['table_schema'],
                ':table_name' => $tableConst['table_name'],
                ':constraint_name' => $tableConst['constraint_name'],
            );
            $constColumns = $this->database->executeQuery($constColumnQuery, $constColumnParams);
            foreach ($constColumns as $constColumn) {
                $relation->addKeyColumn($constColumn, $tableConst['constraint_type']);
            }
        }
    }

    /**
     * Loads columns for a given relation
     * @param Relation $relation
     */
    protected function loadColumnsForRelation(Relation $relation) {
        $sql = new SelectStatement();
        $sql->from('information_schema.columns')
                ->where(sqlstr('table_schema')->equalsTo(':table_schema'))
                ->andWhere(sqlstr('table_name')->equalsTo(':table_name'));

        $params = [':table_schema' => $relation->getSchemaName(), ':table_name' => $relation->getName()];
        foreach ($this->database->executeQuery($sql, $params) as $record) {
            $column = new Column($record);
            $relation->addColumn($column);
        }
    }

}
