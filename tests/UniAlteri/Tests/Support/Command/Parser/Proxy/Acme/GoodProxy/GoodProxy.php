<?php
/**
 * Created by PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, www.uni-alteri.fr
 * Date: 11/01/15
 * Time: 13:27
 */

namespace Acme\GoodProxy;

use UniAlteri\States;
use UniAlteri\States\DI;
use UniAlteri\States\Proxy\Exception;
use UniAlteri\States\Proxy\ProxyInterface;

class WithoutImpl implements ProxyInterface
{
    /**
     * Called to clone an Object
     * @return $this
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * To call a method of the Object.
     * @param  string $name
     * @param  array $arguments
     * @return mixed
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     * @throws Exception\IllegalArgument      if the method's name is not a string
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

    /**
     * To invoke an object as a function
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    /**
     * To get a property of the object
     * @param  string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    /**
     * To test if a property is set for the object.
     * @param  string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __isset($name)
    {
        // TODO: Implement __isset() method.
    }

    /**
     * To update a property of the object.
     * @param  string $name
     * @param  string $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
    }

    /**
     * To remove a property of the object.
     * @param  string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __unset($name)
    {
        // TODO: Implement __unset() method.
    }

    /**
     * To transform the object to a string
     * You cannot throw an exception from within a __toString() method. Doing so will result in a fatal error.
     * @return mixed
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        // TODO: Implement setDIContainer() method.
    }

    /**
     * To return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        // TODO: Implement getDIContainer() method.
    }

    /**
     * To return a unique stable id of the current object. It must identify
     * @return string
     */
    public function getObjectUniqueId()
    {
        // TODO: Implement getObjectUniqueId() method.
    }

    /**
     * To register dynamically a new state for this object
     * @param  string $stateName
     * @param  States\States\StateInterface $stateObject
     * @return $this
     * @throws Exception\IllegalArgument    when the identifier is not a string
     * @throws Exception\IllegalName        when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function registerState($stateName, States\States\StateInterface $stateObject)
    {
        // TODO: Implement registerState() method.
    }

    /**
     * To remove dynamically a state from this object
     * @param  string $stateName
     * @return $this
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function unregisterState($stateName)
    {
        // TODO: Implement unregisterState() method.
    }

    /**
     * To disable all actives states and enable the required states
     * @param  string $stateName
     * @return $this
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function switchState($stateName)
    {
        // TODO: Implement switchState() method.
    }

    /**
     * To enable a loaded states
     * @param $stateName
     * @return $this
     * @throws Exception\StateNotFound   if $stateName does not exist
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function enableState($stateName)
    {
        // TODO: Implement enableState() method.
    }

    /**
     * To disable an active state (not available for calling, but always loaded)
     * @param  string $stateName
     * @return $this
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function disableState($stateName)
    {
        // TODO: Implement disableState() method.
    }

    /**
     * To disable all actives states
     * @return $this
     */
    public function disableAllStates()
    {
        // TODO: Implement disableAllStates() method.
    }

    /**
     * To list all currently available states for this object.
     * @return string[]
     */
    public function listAvailableStates()
    {
        // TODO: Implement listAvailableStates() method.
    }

    /**
     * To list all enable states for this object.
     * @return string[]
     */
    public function listEnabledStates()
    {
        // TODO: Implement listEnabledStates() method.
    }

    /**
     * Check if the current entity is in the required state defined by $stateName
     * @param  string $stateName
     * @return bool
     * @throws Exception\InvalidArgument when $stateName is not a valid string
     */
    public function inState($stateName)
    {
        // TODO: Implement inState() method.
    }

    /**
     * To return the current injection closure object to access to its static properties
     * @return DI\InjectionClosureInterface
     * @throws Exception\UnavailableClosure
     */
    public function getStatic()
    {
        // TODO: Implement getStatic() method.
    }

    /**
     * To return the description of the method
     * @param  string $methodName
     * @param  string $stateName : Return the description for a specific state of the object,
     *                                                    if null, use the current state
     * @return \ReflectionMethod
     * @throws Exception\StateNotFound        is the state required is not available
     * @throws Exception\InvalidArgument      where $methodName or $stateName are not string
     * @throws Exception\MethodNotImplemented when the method is not currently available
     * @throws \Exception                     to rethrows unknown exceptions
     */
    public function getMethodDescription($methodName, $stateName = null)
    {
        // TODO: Implement getMethodDescription() method.
    }

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return int
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function count()
    {
        // TODO: Implement count() method.
    }

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @param  string|int $offset
     * @return bool
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @param  string|int $offset
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * Assigns a value to the specified offset.
     * @param  string|int $offset
     * @param  mixed $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * Unset an offset.
     * @param  string|int $offset
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * Returns the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function current()
    {
        // TODO: Implement current() method.
    }

    /**
     * Returns the key of the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * Moves the current position to the next element.
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * Rewinds back to the first element of the Iterator.
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    /**
     * Seeks to a given position in the iterator.
     * @param  int $position
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function seek($position)
    {
        // TODO: Implement seek() method.
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @return bool
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * Returns an external iterator.
     * @return \Traversable
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * To serialize the object
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     * @return string
     */
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    /**
     * To wake up the object
     * @param  string $serialized
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }

}