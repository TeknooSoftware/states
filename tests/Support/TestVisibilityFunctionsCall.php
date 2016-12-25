<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
if (!function_exists('testCallFromFunctionPrivate')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a private method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    function testCallFromFunctionPrivate()
    {
        global $proxy;
        $proxy->privateTest();
    }
}

if (!function_exists('testCallFromFunctionProtected')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a protected method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    function testCallFromFunctionProtected()
    {
        global $proxy;
        $proxy->protectedTest();
    }
}

if (!function_exists('testCallFromFunctionPublic')) {
    /**
     * Build temp class to test proxy behavior with different scope visibility
     * from an external object to get a description of a public method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     *
     * @return mixed
     */
    function testCallFromFunctionPublic()
    {
        global $proxy;

        return $proxy->publicTest();
    }
}

if (!trait_exists('testCallTrait')) {
    /**
     * Build temp trait to test proxy behavior with different scope visibility
     * from object of a the same class of a inherited class.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    trait testCallTrait
    {
        public function privateMethod()
        {
            global $proxy;

            return $proxy->privateTest();
        }

        public function protectedMethod()
        {
            global $proxy;

            return $proxy->protectedTest();
        }

        public function publicMethod()
        {
            global $proxy;

            return $proxy->publicTest();
        }

        public static function privateMethodStatic()
        {
            global $proxy;

            return $proxy->privateTest();
        }

        public static function protectedMethodStatic()
        {
            global $proxy;

            return $proxy->protectedTest();
        }

        public static function publicMethodStatic()
        {
            global $proxy;

            return $proxy->publicTest();
        }
    }
}

if (!class_exists('testCallFromOtherObject')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a external object to get a description of methods.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    class testCallFromOtherObject
    {
        use testCallTrait;
    }
}
