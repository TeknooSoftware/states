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
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\States;

use UniAlteri\States\Proxy;
use UniAlteri\Tests\Support;

/**
 * Class StateTest
 * Implementation of AbstractStatesTest to test the trait \UniAlteri\States\State\StateTrait and
 * the abstract class \UniAlteri\States\State\AbstractState.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateTest extends AbstractStatesTest
{
    /**
     * Build a basic object to provide only public methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyPublic
     */
    protected function getPublicClassObject(bool $privateMode, string $statedClassName)
    {
        return new Support\MockOnlyPublic($privateMode, $statedClassName);
    }

    /**
     * Build a basic object to provide only protected methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyProtected
     */
    protected function getProtectedClassObject(bool $privateMode, string $statedClassName)
    {
        return new Support\MockOnlyProtected($privateMode, $statedClassName);
    }

    /**
     * Build a basic object to provide only private methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyPrivate
     */
    protected function getPrivateClassObject(bool $privateMode, string $statedClassName)
    {
        return new Support\MockOnlyPrivate($privateMode, $statedClassName);
    }

    /**
     * Build a virtual proxy for test.
     *
     * @return Proxy\ProxyInterface
     */
    protected function getMockProxy()
    {
        return new Support\MockProxy(array());
    }
}
