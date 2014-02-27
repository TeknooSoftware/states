<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

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
