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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * Class MockOnlyProtected
 * Mock class to test the default trait State behavior with protected methods.
 * All methods have not a description to check the state's behavior with these methods.
 *
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class MockOnlyProtected implements StateInterface
{
    use StateTrait;

    protected static function _staticMethod5()
    {
        return function ($a=0, $b=0) {
            return $a+$b;
        };
    }

    /**
     * Standard Method 6.
     *
     * @param $a
     * @param $b
     *
     * @return mixed
     */
    protected function standardMethod6()
    {
        /*
         * @param $a
         * @param $b
         *
         * @return mixed
         */
        return function ($a, $b) {
            return $a + $b;
        };
    }

    /**
     * Final Method 7.
     */
    final protected function finalMethod7()
    {
        return function ($a=0, $b=0) {
            return $a+$b;
        };
    }

    protected function standardMethod8()
    {
        return function ($a=0, $b=0) {
            return $a+$b;
        };
    }
}
