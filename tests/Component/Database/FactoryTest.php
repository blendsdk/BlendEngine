<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database;

use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\Component\Database\StatementResult;
use Blend\Component\Database\Factory\Factory;
use Blend\Component\Model\Model;
use Blend\Component\Database\Database;

class TesterModel extends Model {

    public function getId() {
        return $this->getValue('id');
    }

    public function getField1() {
        return $this->getValue('field1');
    }

}

class TesterFactory extends Factory {

    public function __construct(Database $database) {
        parent::__construct($database, TesterModel::class);
        $this->relation = 'public.table1';
    }

    public function getOneByTest($selectColumns, $byColumns) {
        return $this->getOneBy($selectColumns, $byColumns);
    }

    public function getManyByTest($selectColumns, array $byColumns, $orderDirective = null, $offsetLimitDirective = null) {
        return $this->getManyBy($selectColumns, $byColumns, $orderDirective, $offsetLimitDirective);
    }

    public function getAllTest($selectColumns, $orderDirective = null, $offsetLimitDirective = null) {
        return $this->getAll($selectColumns, $orderDirective, $offsetLimitDirective);
    }

    public function deleteOneByTest($byColumns) {
        return $this->deleteOneBy($byColumns);
    }

}

/**
 * Description of FactoryTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FactoryTest extends DatabaseTestBase {

    /**
     * @expectedException \Blend\Component\Exception\DatabaseQueryException
     */
    public function testDeleteOnRecordMultiple() {
        $factory = new TesterFactory(self::$currentDatabase);
        $model = $factory->deleteOneByTest(['field3' => 0]);
    }

    public function testDeleteOnRecordNoDelete() {
        $factory = new TesterFactory(self::$currentDatabase);
        $model = $factory->deleteOneByTest(['id' => 9999]);
        $this->assertNull($model);
    }

    public function testDeleteOneRecord() {
        $factory = new TesterFactory(self::$currentDatabase);
        $model = $factory->deleteOneByTest(['id' => 10]);
        $this->assertEquals(10, $model->getId());
    }

    public function testGetRecords() {
        $factory = new TesterFactory(self::$currentDatabase);
        $model = $factory->getOneByTest(['*'], ['id' => 2]);
        $this->assertEquals($model->getField1(), 'value2');

        $list = $factory->getManyByTest(['*'], ['field3' => 0]);
        $this->assertCount(9, $list);

        $list2 = $factory->getManyByTest(['*'], ['true' => true], ['id' => 'DESC', 'field1' => 'ASC']);
        $this->assertEquals(100, $list2[0]->getId());

        $list3 = $factory->getManyByTest(['*'], ['true' => true], null, ['offset' => 10, 'limit' => '2']);
        $this->assertCount(2, $list3);

        $list4 = $factory->getManyByTest(
                ['*']
                , ['true' => true]
                , ['id' => 'DESC', 'field1' => 'ASC']
                , ['offset' => 10, 'limit' => '3']
        );
        $this->assertCount(3, $list4);


        $list5 = $factory->getAllTest(null);
        $this->assertCount(100, $list5);
    }

    public static function getTestingDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'factory_test'
        ];
    }

    protected function setUp() {
        self::$currentDatabase->executeQuery('drop table if exists table1 cascade');
        self::$currentDatabase->executeQuery('create table table1(id serial not null primary key,field1 varchar, field2 integer,field3 integer)');
        for ($a = 0; $a != 100; $a++) {
            $b = (1 + $a);
            self::$currentDatabase->insert('table1', [
                'id' => $b,
                'field1' => 'value' . $b,
                'field2' => $b,
                'field3' => intval($b / 10)
            ]);
        }
    }

}
