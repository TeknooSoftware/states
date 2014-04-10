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

if (!function_exists('testGetMethodDescriptionFromFunctionPrivate')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a private method
     */
    function testGetMethodDescriptionFromFunctionPrivate() {
        global $proxy;
        $proxy->getMethodDescription('privateTest');
    }
}

if (!function_exists('testGetMethodDescriptionFromFunctionProtected')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a protected method
     */
    function testGetMethodDescriptionFromFunctionProtected() {
        global $proxy;
        $proxy->getMethodDescription('protectedTest');
    }
}

if (!function_exists('testGetMethodDescriptionFromFunctionPublic')) {
    /**
     * Build temp func to test proxy behavior with scope visibility
     * from a function to get a description of a public method
     * @return \ReflectionMethod
     *
     */
    function testGetMethodDescriptionFromFunctionPublic() {
        global $proxy;
        return $proxy->getMethodDescription('publicTest');
    }
}

if (!trait_exists('testGetMethodDescriptionTrait')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a object to get a description of methods
    trait testGetMethodDescriptionTrait{
        public function privateMethod() {
            global $proxy;
            return $proxy->getMethodDescription('privateTest');
        }

        public function protectedMethod() {
            global $proxy;
            return $proxy->getMethodDescription('protectedTest');
        }

        public function publicMethod() {
            global $proxy;
            return $proxy->getMethodDescription('publicTest');
        }

        public static function privateMethodStatic() {
            global $proxy;
            return $proxy->getMethodDescription('privateTest');
        }

        public static function protectedMethodStatic() {
            global $proxy;
            return $proxy->getMethodDescription('protectedTest');
        }

        public static function publicMethodStatic() {
            global $proxy;
            return $proxy->getMethodDescription('publicTest');
        }
    }
}

if (!class_exists('testGetMethodDescriptionFromOtherObject')) {
    //Build temp func to test proxy behavior with scope visibility
    //from a external object to get a description of methods
    class testGetMethodDescriptionFromOtherObject{
        use testGetMethodDescriptionTrait;
    }
}
