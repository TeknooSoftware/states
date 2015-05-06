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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.1.0
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\Proxy;
use UniAlteri\States\States;

/**
 * Class MockFinderWithArray
 * Mock finder to test behavior of proxies and factories
 * This mock return ArrayObject instead of array values in the method listStates.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockFinderWithArray extends MockFinder
{
    /**
     * List all available state object of the stated class.
     *
     * @return string[]
     */
    public function listStates()
    {
        if (empty(static::$ignoreDefaultState)) {
            return new \ArrayObject(
                array(
                    'MockState1',
                    'MockState2',
                    Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                    'MockState3',
                )
            );
        } else {
            return new \ArrayObject(
                array(
                    'MockState1',
                    'MockState2',
                    'MockState3',
                )
            );
        }
    }
}
