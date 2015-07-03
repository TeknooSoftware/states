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
 */

namespace UniAlteri\States\Proxy;

use UniAlteri\States;
use UniAlteri\States\DI;

/**
 * Interface ProxyInterface
 * Interface to define "Proxy Object" used in this library to create stated object.
 *
 * A stated object is a proxy, configured for its stated class, with its different stated objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states' methods) points the proxy object.
 *
 * The library creates an alias with the proxy class name and this default proxy to simulate a dedicated proxy
 * to this class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface ProxyInterface
{
    /**
     * Name of the default state to load automatically in the construction.
     */
    const DEFAULT_STATE_NAME = 'StateDefault';

    /**************************
     *** Container Management *
     **************************/

    /**
     * To register a DI container for this object.
     * @api
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container): ProxyInterface;

    /**
     * To return the DI Container used for this object.
     * @api
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer(): DI\ContainerInterface;

    /**
     * Called to clone an Object.
     * @api
     *
     * @return $this
     */
    public function __clone();

    /***********************
     *** States Management *
     ***********************/

    /**
     * To register dynamically a new state for this object.
     * @api
     *
     * @param string                       $stateName
     * @param States\States\StateInterface $stateObject
     *
     * @return $this
     *
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function registerState(string $stateName, States\States\StateInterface $stateObject): ProxyInterface;

    /**
     * To remove dynamically a state from this object.
     * @api
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function unregisterState(string $stateName): ProxyInterface;

    /**
     * To disable all actives states and enable the required states.
     * @api
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function switchState(string $stateName): ProxyInterface;

    /**
     * To enable a loaded states.
     * @api
     *
     * @param $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound   if $stateName does not exist
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function enableState(string $stateName): ProxyInterface;

    /**
     * To disable an active state (not available for calling, but always loaded).
     * @api
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function disableState(string $stateName): ProxyInterface;

    /**
     * To disable all actives states.
     * @api
     *
     * @return $this
     */
    public function disableAllStates(): ProxyInterface;

    /**
     * To list all currently available states for this object.
     * @api
     *
     * @return string[]
     */
    public function listAvailableStates();

    /**
     * To list all enable states for this object.
     * @api
     *
     * @return string[]
     */
    public function listEnabledStates();

    /**
     * To return the list of all states entity available for this object
     * @api
     *
     * @return \ArrayAccess|States\States\StateInterface[]
     */
    public function getStatesList();

    /**
     * Check if the current entity is in the required state defined by $stateName.
     * @api
     *
     * @param string $stateName
     *
     * @return bool
     */
    public function inState(string $stateName): bool;

    /*******************
     * Methods Calling *
     *******************/

    /**
     * To call a method of the Object.
     * @api
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __call(string $name, array $arguments);

    /**
     * To return the description of the method.
     * @api
     *
     * @param string $methodName
     * @param string $stateName  : Return the description for a specific state of the object,
     *                           if null, use the current state
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\StateNotFound        is the state required is not available
     * @throws Exception\MethodNotImplemented when the method is not currently available
     * @throws \Exception                     to rethrows unknown exceptions
     */
    public function getMethodDescription(string $methodName, string $stateName = null): \ReflectionMethod;

    /**
     * To invoke an object as a function.
     * @api
     *
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __invoke(...$args);

    /*******************
     * Data Management *
     *******************/

    /**
     * To get a property of the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __get(string $name);

    /**
     * To test if a property is set for the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __isset(string $name);

    /**
     * To update a property of the object.
     * @api
     *
     * @param string $name
     * @param string $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __set(string $name, $value);

    /**
     * To remove a property of the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __unset(string $name);

    /**
     * To transform the object to a string
     * You cannot throw an exception from within a __toString() method. Doing so will result in a fatal error.
     * @api
     *
     * @return mixed
     */
    public function __toString();

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @api
     *
     * @return int
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function count(): int;

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @api
     *
     * @param string|int $offset
     *
     * @return bool
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetExists($offset);

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @api
     *
     * @param string|int $offset
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetGet($offset);

    /**
     * Assigns a value to the specified offset.
     * @api
     *
     * @param string|int $offset
     * @param mixed      $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetSet($offset, $value);

    /**
     * Unset an offset.
     * @api
     *
     * @param string|int $offset
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetUnset($offset);

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     * @api
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function current();

    /**
     * Returns the key of the current element.
     * @api
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function key();

    /**
     * Moves the current position to the next element.
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function next();

    /**
     * Rewinds back to the first element of the Iterator.
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function rewind();

    /**
     * Seeks to a given position in the iterator.
     * @api
     *
     * @param int $position
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function seek($position);

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @api
     *
     * @return bool
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function valid();

    /**
     * Returns an external iterator.
     * @api
     *
     * @return \Traversable
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function getIterator(): \Traversable;

    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object.
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     *
     * @return string
     */
    public function serialize(): string;

    /**
     * To wake up the object.
     * @api
     *
     * @param string $serialized
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function unserialize($serialized);
}
