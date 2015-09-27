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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\State\AbstractState;

/**
 * Class MockOnlyProtected
 * Mock class to test the default trait State behavior with protected methods.
 * All methods have not a description to check the state's behavior with these methods.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockOnlyProtected extends AbstractState
{
    protected static function _staticMethod5()
    {
    }

    /**
     * Standard Method 6.
     *
     * @param $a
     * @param $b
     *
     * @return mixed
     */
    protected function standardMethod6($a, $b)
    {
        return $a + $b;
    }

    /**
     * Final Method 7.
     */
    final protected function finalMethod7()
    {
    }

    protected function standardMethod8()
    {
    }
}
