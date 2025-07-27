<?php

declare(strict_types=1);

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
if (!function_exists('testCallFromFunctionPrivate')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a private method.
     *
     *
     *
     * @link        https://teknoo.software/libraries/states Project website
     *
     * @license     https://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richard@teknoo.software>
     */
    function testCallFromFunctionPrivate(): void
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
     * @link        https://teknoo.software/libraries/states Project website
     *
     * @license     https://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richard@teknoo.software>
     */
    function testCallFromFunctionProtected(): void
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
     * @link        https://teknoo.software/libraries/states Project website
     *
     * @license     https://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richard@teknoo.software>
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
     * @link        https://teknoo.software/libraries/states Project website
     *
     * @license     https://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richard@teknoo.software>
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
     * @link        https://teknoo.software/libraries/states Project website
     *
     * @license     https://teknoo.software/license/mit         MIT License
     * @author      Richard Déloge <richard@teknoo.software>
     */
    class testCallFromOtherObject
    {
        use testCallTrait;
    }
}
