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
 * Mock factory file to test command for cli helper
 */

namespace Acme\GoodProxy;

use UniAlteri\States\State\StateInterface;
use UniAlteri\States\Proxy\Exception;
use UniAlteri\States\Proxy\ProxyInterface;

class GoodProxy implements ProxyInterface
{
    /**
     * Called to clone an Object.
     *
     * @return $this
     */
    public function __clone()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __call(\string $name, array $arguments)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(...$args)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function registerState(\string $stateName, StateInterface $stateObject): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterState(\string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function switchState(\string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function enableState(\string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function disableState(\string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function disableAllStates(): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function listAvailableStates()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function listEnabledStates()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getStatesList()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function inState(\string $stateName): \bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription(\string $methodName, \string $stateName = null): \ReflectionMethod
    {
    }
}
