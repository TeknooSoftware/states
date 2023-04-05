<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support;

use Teknoo\States\Proxy\Exception;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

/**
 * Class MockProxy
 * Mock proxy to tests factories behavior and trait state behavior.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MockProxy implements ProxyInterface, \Stringable
{
    /**
     * Local registry of loaded states, to simulate a real proxy.
     *
     * @var array
     */
    protected $states = [];

    /**
     * Local registry of active states, to simulate a real proxy.
     *
     * @var array
     */
    protected $actives = [];

    /**
     * @param mixed $args
     */
    public function __construct(public ?array $args)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        //Not used in tests
    }

    /***********************
     *** States Management *
     ***********************/

    protected static function statesListDeclaration(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface
    {
        //Simulate real behavior
        $this->states[$stateName] = $stateObject;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterState(string $stateName): ProxyInterface
    {
        //Simulate real behavior
        if (isset($this->states[$stateName])) {
            unset($this->states[$stateName]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function switchState(string $stateName): ProxyInterface
    {
        //Simulate real behavior
        $this->actives = [$stateName => $stateName];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enableState(string $stateName): ProxyInterface
    {
        //Simulate real behavior
        $this->actives[$stateName] = $stateName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableState(string $stateName): ProxyInterface
    {
        //Simulate real behavior
        if (isset($this->actives[$stateName])) {
            unset($this->actives[$stateName]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableAllStates(): ProxyInterface
    {
        //Simulate real behavior
        $this->actives = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInState(array $statesNames, callable $callback, bool $allRequired = false): ProxyInterface
    {
        foreach ($statesNames as $stateName) {
            if (in_array(strtolower(str_replace('_', '', $stateName)), $this->actives)) {
                $callback($this->actives);
            }
        }
    }

    public function isNotInState(array $statesNames, callable $callback, bool $allForbidden = false): ProxyInterface
    {
        foreach ($statesNames as $stateName) {
            if (!in_array(strtolower(str_replace('_', '', $stateName)), $this->actives)) {
                $callback($this->actives);
            }
        }
    }

    /*******************
     * Methods Calling *
     *******************/

    /**
     * {@inheritdoc}
     */
    public function __call(string $name, array $arguments): mixed
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(...$args)
    {
        //Not used in tests
    }

    /*******************
     * Data Management *
     *******************/

    /**
     * {@inheritdoc}
     */
    public function __get(string $name)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function __isset(string $name)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function __set(string $name, $value)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function __unset(string $name)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        //Not used in tests
        return '';
    }

    /****************
     * Array Access *
     ****************/

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        //Not used in tests
    }

    /************
     * Iterator *
     ************/

    /**
     * {@inheritdoc}
     */
    public function current(): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function key(): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        //Not used in tests
    }

    /*****************
     * Serialization *
     *****************/

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function getState($stateName)
    {
        return $this->states[$stateName];
    }
}
