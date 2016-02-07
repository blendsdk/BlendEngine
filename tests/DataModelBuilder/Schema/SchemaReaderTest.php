<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\DataModelBuilder\Schema;

use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\DataModelBuilder\Schema\SchemaReader;

/**
 * Description of SchemaReaderTest
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaReaderTest extends DatabaseTestBase {

    public function testSanity() {
        $reader = new SchemaReader(self::$currentDatabase);
        $schemas = $reader->load();

        $this->assertCount(4, $schemas);

        // test tables
        $this->assertCount(0, array_diff(array('p1', 'p2'), array_keys($schemas['public']->getRelations())));
        $this->assertCount(0, array_diff(array('s1', 's2', 's3'), array_keys($schemas['schema1']->getRelations())));
        $this->assertCount(0, array_diff(array('s1', 's2', 's3', 's4'), array_keys($schemas['schema2']->getRelations())));

        $rels = $schemas['public']->getRelations();
        $this->assertCount(0, array_diff(array('col1'), array_keys($rels['p1']->getColumns())));
        $this->assertCount(0, array_diff(array('col1', 'col2'), array_keys($rels['p2']->getColumns())));

        $rels = $schemas['schema1']->getRelations();
        $this->assertCount(0, array_diff(array('col1'), array_keys($rels['s1']->getColumns())));
        $this->assertCount(0, array_diff(array('col1', 'col2'), array_keys($rels['s2']->getColumns())));
        $this->assertCount(0, array_diff(array('col1', 'col2', 'col3'), array_keys($rels['s3']->getColumns())));

        $keysSchema = $schemas['k'];
        $keySchemaRelations = $keysSchema->getRelations();
        $table_with_pk = $keySchemaRelations['table_with_pk'];
        $keys = $table_with_pk->getLocalKeys();
        $this->assertArrayHasKey('PRIMARY KEY', $keys);
        $this->assertCount(1, $keys['PRIMARY KEY']);

        $table_with_two_pk = $keySchemaRelations['table_with_two_pk'];
        $keys = $table_with_two_pk->getLocalKeys();
        $this->assertArrayHasKey('PRIMARY KEY', $keys);
        $this->assertCount(2, $keys['PRIMARY KEY']);

        $table_with_unique_key = $keySchemaRelations['table_with_unique_key'];
        $keys = $table_with_unique_key->getLocalKeys();
        $this->assertCount(1, $keys);
    }

    public static function getTestingDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => __CLASS__
        ];
    }

    public static function setUpSchema() {
        self::$currentDatabase->executeScript(file_get_contents(__DIR__ . '/scripts/schema.sql'));
    }

}
