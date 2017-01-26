<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\Schema;

use Blend\Component\Database\Schema\SchemaReader;
use Blend\Tests\Component\Database\DatabaseTestBase;

class SchemaReaderTest extends DatabaseTestBase
{
    public static function getTestingDatabaseConfig()
    {
        return array(
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'database_schemareader_test',
        );
    }

    public function testSchemaReader()
    {
        $schemas = (new SchemaReader(self::$currentDatabase))->read();

        $this->assertTrue(is_array($schemas));
        $this->assertArrayHasKey('public', $schemas);

        /* @var $public Schema */
        $public = $schemas['public'];
        $relations = $public->getRelations();
        $this->assertArrayHasKey('table1', $relations);
        $this->assertArrayHasKey('table2', $relations);

        /* @var $table1 Relation */
        $table1 = $relations['table1'];
        $columns = $table1->getColumns();
        $this->assertEquals(4, count($columns));

        /* @var $address_table Relation */
        $address_table = $relations['address_table'];
        $this->assertEquals(1, count($address_table->getConstraints()));

        $pkey = $address_table->getConstraint('address_table_pkey');
        $keyColumns = $pkey->getColumns();
        $this->assertEquals(2, count($keyColumns));

        $const_table = $relations['const_table'];
        $this->assertEquals(2, count($const_table->getConstraints()));

        /* @var $table1 Relation */
        $child_table = $relations['child_table'];
        $this->assertEquals(2, count($child_table->getConstraints()));
    }

    public static function setUpSchema()
    {
        self::$currentDatabase->executeScript(
                file_get_contents(__DIR__ . '/../scripts/schema.sql'));
    }
}
