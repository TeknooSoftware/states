<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class MockProxy implements ProxyInterface
{
    /**
     * To test args passed by factory.
     *
     * @var null|array
     */
    public $args = null;

    /**
     * Local registry of loaded states, to simulate a real proxy.
     *
     * @var array
     */
    protected $states = array();

    /**
     * Local registry of active states, to simulate a real proxy.
     *
     * @var array
     */
    protected $actives = array();

    /**
     * @param mixed $arguments
     */
    public function __construct($arguments)
    {
        $this->args = $arguments;
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
        $this->actives = array($stateName => $stateName);

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
        $this->actives = array();

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
    public function offsetExists($offset)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        //Not used in tests
    }

    /************
     * Iterator *
     ************/

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        //Not used in tests
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
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
    public function unserialize($serialized)
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
