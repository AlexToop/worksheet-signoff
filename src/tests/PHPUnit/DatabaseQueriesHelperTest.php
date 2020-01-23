<?php

require_once __DIR__ . '\..\..\www\htdocs\scripts\DatabaseQueriesHelper.php';

class DatabaseQueriesHelperTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        // if needed in future
    }

    public function test_gets_correct_indexes_for_column_names_in_order()
    {
        $result = getIndexUserFirstLast([["Username", "First name", "Last name"], ["Example", "Example", "Example"]]);
        $this->assertEquals([0, 1, 2], $result);
    }


    public function test_gets_correct_indexes_for_column_names_mixed()
    {
        $result = getIndexUserFirstLast([["First name", "Last name", "Username"], ["Example", "Example", "Example"]]);
        $this->assertEquals([2, 0, 1], $result);
    }


    public function test_gets_correct_indexes_for_column_names_one_missing()
    {
        $result = getIndexUserFirstLast([["First name", "Example", "Username"], ["Example", "Example", "Example"]]);
        $this->assertEquals([2, 0, null], $result);
    }


    public function test_gets_correct_indexes_for_column_names_wrong_row()
    {
        $result = getIndexUserFirstLast([["Example", "Example", "Example"], ["First name", "Example", "Username"]]);
        $this->assertEquals([null, null, null], $result);
    }

}
