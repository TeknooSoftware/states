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

namespace Teknoo\Tests\States\States;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Class StateTest
 * Implementation of AbstractStatesTest to test the trait \Teknoo\States\States\StateTrait and
 * the abstract class \Teknoo\States\States\AbstractState.
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
class StateTest extends AbstractStatesTest
{
    /**
     * Build a basic object to provide only public methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyPublic
     */
    protected function getPublicClassObject($initializeContainer = true)
    {
        return new Support\MockOnlyPublic($initializeContainer);
    }

    /**
     * Build a basic object to provide only protected methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyProtected
     */
    protected function getProtectedClassObject($initializeContainer = true)
    {
        return new Support\MockOnlyProtected($initializeContainer);
    }

    /**
     * Build a basic object to provide only private methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyPrivate
     */
    protected function getPrivateClassObject($initializeContainer = true)
    {
        return new Support\MockOnlyPrivate($initializeContainer);
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
