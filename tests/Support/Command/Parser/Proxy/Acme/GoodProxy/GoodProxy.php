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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * Mock factory file to test command for cli helper
 */
namespace Acme\GoodProxy;

use Teknoo\States\State\StateInterface;
use Teknoo\States\Proxy\ProxyInterface;

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
    public function __call(string $name, array $arguments)
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
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterState(string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function switchState(string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function enableState(string $stateName): ProxyInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function disableState(string $stateName): ProxyInterface
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
    public function inState(string $stateName): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription(string $methodName, string $stateName = null): \ReflectionMethod
    {
    }
}
