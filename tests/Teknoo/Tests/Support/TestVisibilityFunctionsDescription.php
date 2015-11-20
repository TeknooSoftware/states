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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
if (!function_exists('testGetMethodDescriptionFromFunctionPrivate')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a private method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/states/license/mit         MIT License
     * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    function testGetMethodDescriptionFromFunctionPrivate()
    {
        global $proxy;
        $proxy->getMethodDescription('privateTest');
    }
}

if (!function_exists('testGetMethodDescriptionFromFunctionProtected')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a protected method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/states/license/mit         MIT License
     * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    function testGetMethodDescriptionFromFunctionProtected()
    {
        global $proxy;
        $proxy->getMethodDescription('protectedTest');
    }
}

if (!function_exists('testGetMethodDescriptionFromFunctionPublic')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a public method.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/states/license/mit         MIT License
     * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     *
     * @return \ReflectionMethod *
     */
    function testGetMethodDescriptionFromFunctionPublic()
    {
        global $proxy;

        return $proxy->getMethodDescription('publicTest');
    }
}

if (!trait_exists('testGetMethodDescriptionTrait')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from object of a the same class of a inherited class.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/states/license/mit         MIT License
     * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    trait testGetMethodDescriptionTrait
    {
        public function privateMethod()
        {
            global $proxy;

            return $proxy->getMethodDescription('privateTest');
        }

        public function protectedMethod()
        {
            global $proxy;

            return $proxy->getMethodDescription('protectedTest');
        }

        public function publicMethod()
        {
            global $proxy;

            return $proxy->getMethodDescription('publicTest');
        }

        public static function privateMethodStatic()
        {
            global $proxy;

            return $proxy->getMethodDescription('privateTest');
        }

        public static function protectedMethodStatic()
        {
            global $proxy;

            return $proxy->getMethodDescription('protectedTest');
        }

        public static function publicMethodStatic()
        {
            global $proxy;

            return $proxy->getMethodDescription('publicTest');
        }
    }
}

if (!class_exists('testGetMethodDescriptionFromOtherObject')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a external object to get a description of methods.
     *
     *
     *
     * @link        http://teknoo.software/states Project website
     *
     * @license     http://teknoo.software/states/license/mit         MIT License
     * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <richarddeloge@gmail.com>
     */
    class testGetMethodDescriptionFromOtherObject
    {
        use testGetMethodDescriptionTrait;
    }
}
