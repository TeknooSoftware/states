<?php
require_once dirname(__FILE__) . '/TestHelper.php';


class UniAlteri_States_AllTests extends PHPUnit_Framework_TestSuite{
    public static function main (){
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite (){
        $suite = new PHPUnit_Framework_TestSuite('Uni Alteri States Tests');
        $suite->addTest(Repository_Test_Traits_AllTests::suite());

        return $suite;
    }
}
