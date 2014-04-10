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
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

if (!function_exists('testMagicFromFunction')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a private method
     */
    function testMagicFromFunction() {
        global $proxy;
        global $method;
        $proxy->{$method}();
    }
}

if (!trait_exists('testMagicTrait')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a object to get a description of methods
    trait testMagicTrait{
        public function magicMethod() {
            global $proxy;
            global $method;
            $proxy->{$method}();
        }

        public static function magicMethodStatic() {
            global $proxy;
            global $method;
            $proxy->{$method}();
        }
    }
}

if (!class_exists('testMagicFromOtherObject')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a external object to get a description of methods
    class testMagicFromOtherObject{
        use testMagicTrait;
    }
}
