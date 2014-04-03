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

if (!function_exists('testCallFromFunctionPrivate')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a private method
     */
    function testCallFromFunctionPrivate() {
        global $proxy;
        $proxy->privateTest();
    }
}

if (!function_exists('testCallFromFunctionProtected')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a protected method
     */
    function testCallFromFunctionProtected() {
        global $proxy;
        $proxy->protectedTest();
    }
}

if (!function_exists('testCallFromFunctionPublic')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a public method
     * @return \ReflectionMethod
     *
     */
    function testCallFromFunctionPublic() {
        global $proxy;
        return $proxy->publicTest();
    }
}

if (!trait_exists('testCallTrait')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a object to get a description of methods
    trait testCallTrait{
        public function privateMethod() {
            global $proxy;
            return $proxy->privateTest();
        }

        public function protectedMethod() {
            global $proxy;
            return $proxy->protectedTest();
        }

        public function publicMethod() {
            global $proxy;
            return $proxy->publicTest();
        }

        public static function privateMethodStatic() {
            global $proxy;
            return $proxy->privateTest();
        }

        public static function protectedMethodStatic() {
            global $proxy;
            return $proxy->protectedTest();
        }

        public static function publicMethodStatic() {
            global $proxy;
            return $proxy->publicTest();
        }
    }
}

if (!class_exists('testCallFromOtherObject')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a external object to get a description of methods
    class testCallFromOtherObject{
        use testCallTrait;
    }
}
