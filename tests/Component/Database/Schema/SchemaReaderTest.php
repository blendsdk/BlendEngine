<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\Schema;

use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\Component\Database\Schema\SchemaReader;

/**
 * Description of SchemaReaderTest
 * 
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaReaderTest extends DatabaseTestBase {
    
    public function testSanity() {
        $reader = new SchemaReader(self::$currentDatabase);
        $reader->load();
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
