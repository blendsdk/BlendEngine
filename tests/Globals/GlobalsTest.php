<?php

namespace Blend\Tests\Globals;

/**
 * Test class for Globals
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class GlobalsTest extends \PHPUnit_Framework_TestCase {

    public function testStrIdentifier() {
        $this->assertEquals('getCustomerEmail()', str_identifier('customer_email', 'get', '()'));
    }

    public function testRenderPHPTemplate() {
        $root = dirname(__FILE__) . '/fixtures';
        $result = render_php_template($root . '/template1.php', array(
            'firstname' => 'Darth',
            'lastname' => 'Vader'
        ));
        $this->assertEquals('Hello Darth Vader', $result);

        $outfile = $root . '/out.txt';
        render_php_template($root . '/template1.php', array(
            'firstname' => 'Luke',
            'lastname' => 'Skywalker'
                ), $outfile);

        $this->assertEquals('Hello Luke Skywalker', file_get_contents($outfile));
        unlink($outfile);
    }

    public function testArrayRemoveNulls() {
        $data = [1, 2, null, 4];
        $result = array_remove_nulls($data);
        $this->assertCount(3, $result);
    }

    public function testStrReplaceTemplate() {
        $template = "@par1 @par2 @par1 @par2";
        $result = str_replace_template($template, array(
            '@par1' => 'A',
            '@par2' => 'B'
        ));
        $this->assertEquals('A B A B', $result);
    }

    public function testArrayReindexMulti() {
        $data = array(
            array('country' => 'NL', 'city' => 'AMS', 'single' => true),
            array('country' => 'NL', 'city' => 'DHG', 'single' => true),
            array('country' => 'US', 'city' => 'LAX'),
        );

        $test1 = array_reindex($data, function($item) {
            return $item['country'];
        });

        $test2 = array_reindex($data, function($item) {
            return $item['country'];
        }, true);

        $this->assertCount(2, $test1);
        $this->assertCount(2, $test1['NL']);
        $this->assertCount(1, $test1['US']);

        $this->assertCount(3, $test2['NL']);
        $this->assertCount(2, $test2['US']);
    }

}
