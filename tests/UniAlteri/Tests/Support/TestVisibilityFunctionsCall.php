<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

if (!function_exists('testCallFromFunctionPrivate')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a private method
     *
     * @package     States
     * @subpackage  Tests
     * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
     * @link        http://teknoo.it/states Project website
     * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <r.deloge@uni-alteri.com>
     */
    function testCallFromFunctionPrivate() {
        global $proxy;
        $proxy->privateTest();
    }
}

if (!function_exists('testCallFromFunctionProtected')) {
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a function to get a description of a protected method
     *
     * @package     States
     * @subpackage  Tests
     * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
     * @link        http://teknoo.it/states Project website
     * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <r.deloge@uni-alteri.com>
     */
    function testCallFromFunctionProtected() {
        global $proxy;
        $proxy->protectedTest();
    }
}

if (!function_exists('testCallFromFunctionPublic')) {
    /**
     * Build temp class to test proxy behavior with different scope visibility
     * from an external object to get a description of a public method
     *
     * @package     States
     * @subpackage  Tests
     * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
     * @link        http://teknoo.it/states Project website
     * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <r.deloge@uni-alteri.com>
     * @return mixed
     */
    function testCallFromFunctionPublic() {
        global $proxy;
        return $proxy->publicTest();
    }
}

if (!trait_exists('testCallTrait')) {
    /**
     * Build temp trait to test proxy behavior with different scope visibility
     * from object of a the same class of a inherited class
     *
     * @package     States
     * @subpackage  Tests
     * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
     * @link        http://teknoo.it/states Project website
     * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <r.deloge@uni-alteri.com>
     */
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
    /**
     * Build temp functions to test proxy behavior with different scope visibility
     * from a external object to get a description of methods
     *
     * @package     States
     * @subpackage  Tests
     * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
     * @link        http://teknoo.it/states Project website
     * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
     * @author      Richard Déloge <r.deloge@uni-alteri.com>
     */
    class testCallFromOtherObject{
        use testCallTrait;
    }
}
