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
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\Loader\Exception;
use UniAlteri\States\Proxy;
use UniAlteri\States\Loader;
use UniAlteri\States\States;

class VirtualFinderWithArray extends VirtualFinder
{
    /**
     * List all available state object of the stated class
     * @return string[]
     */
    public function listStates()
    {
        if (empty(static::$ignoreDefaultState)) {
            return new \ArrayObject(
                array(
                    'VirtualState1',
                    'VirtualState2',
                    Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                    'VirtualState3'
                )
            );
        } else {
            return new \ArrayObject(
                array(
                    'VirtualState1',
                    'VirtualState2',
                    'VirtualState3'
                )
            );
        }
    }
}