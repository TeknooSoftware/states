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
 * @subpackage  Proxy
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Proxy;

use \UniAlteri\States;
use \UniAlteri\States\DI;

/**
 * Interface ProxyInterface
 * @package UniAlteri\States\Proxy
 * Interface to define "Proxy Object" used in this library to create stated object.
 *
 * A stated object is a proxy, configured for its stated class, with its differents states objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states'methods) points the proxy object.
 */
interface ProxyInterface extends
    States\ObjectInterface,
    \Serializable,
    \ArrayAccess,
    \SeekableIterator,
    \Countable
{
    const DEFAULT_STATE_NAME = 'Default';

    /***********************
     *** States Management *
     ***********************/

    /**
     * Register dynamically a new state for this object
     * @param string $stateName
     * @param States\States\StateInterface $stateObject
     * @return $this
     */
    public function registerState($stateName, States\States\StateInterface $stateObject);

    /**
     * Remove dynamically a state from this object
     * @param string $stateName
     * @return $this
     */
    public function unregisterState($stateName);

    /**
     * Disable all actives states and load the required states
     * @param string $stateName
     * @return $this
     */
    public function switchState($stateName);

    /**
     * Enable a loaded states
     * @param $stateName
     * @return $this
     * @throws Exception\StateNotFound if $stateName does not exist
     */
    public function enableState($stateName);

    /**
     * Disable an active state (not available for calling, but already loaded)
     * @param string $stateName
     * @return $this
     */
    public function disableState($stateName);

    /**
     * Disable all actives states
     * @return $this
     */
    public function disableAllStates();

    /**
     * List all available states for this object. Include added dynamically states, exclude removed dynamically states
     * @return string[]
     */
    public function listAvailableStates();

    /**
     * List all enable states for this object.
     * @return string[]
     */
    public function listActivesStates();

    /**
     * Return the current injection closure object to access to its static properties
     * @return DI\InjectionClosureInterface
     * @throws Exception\UnavailableClosure
     */
    public function getStatic();

    /*******************
     * Methods Calling *
     *******************/

    /**
     * Call a method of the Object.
     * Hooks and event are automatically called
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __call($name, $arguments);

    /**
     * Return the description of the method
     * @param string $methodName
     * @param string $stateName : Return the description for a specific state of the object, if null, use the current state
     * @return \ReflectionMethod
     * @throws Exception\StateNotFound is the state required is not available
     */
    public function getMethodDescription($methodName, $stateName = null);

    /**
     * To invoke an object as a function
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __invoke();


    /*******************
     * Data Management *
     *******************/

    /**
     * Get a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __get($name);

    /**
     * Test if a property is set for the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __isset($name);

    /**
     * Update a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @param string $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __set($name, $value);

    /**
     * To remove a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __unset($name);

    /**
     * To transform the object to a string
     * Hooks and event are automatically called
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __toString();

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return int
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function count();

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @param string|int $offset
     * @return bool
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetExists($offset);

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @param string|int $offset
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetGet($offset);

    /**
     * Assigns a value to the specified offset.
     * @param string|int $offset
     * @param mixed $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetSet($offset, $value);

    /**
     * Unsets an offset.
     * @param string|int $offset
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetUnset($offset);

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function current();

    /**
     * Returns the key of the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function key();

    /**
     * Moves the current position to the next element.
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function next();

    /**
     * Rewinds back to the first element of the Iterator.
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function rewind();

    /**
     * Seeks to a given position in the iterator.
     * @param int $position
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function seek($position);

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @return bool
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function valid();

    /**
     * Returns an external iterator.
     * @return \Traversable
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function getIterator();

    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object
     * Hooks and event are automatically called
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     * @return string
     */
    public function serialize();

    /**
     * To wake up the object
     * Hooks and event are automatically called
     * @param string $serialized
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function unserialize($serialized);
}
