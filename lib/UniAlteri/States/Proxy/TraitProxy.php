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
 * @project     States
 * @category    Proxy
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Proxy;

use \UniAlteri\States\DI;
use \UniAlteri\States;

trait TraitProxy
{
    /**
     * DI Container to use for this object
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Unique identifier of this object
     * @var string
     */
    protected $_uniqueId = null;

    /**
     * List of currently enabled states
     * @var States\States\StateInterface[]
     */
    protected $_activesStates = null;

    /**
     * List of available states for this stated object
     * @var States\States\StateInterface[]
     */
    protected $_states = null;

    /**
     * Current closure called, if not closure called, return null
     * @var DI\InjectionClosureInterface
     */
    protected $_currentInjectionClosure = null;

    /**
     * Internal method to find closure and call it
     * @param string $methodName
     * @param array $arguments of the call
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     * @throws \Exception
     */
    protected function _callThroughState($methodName, array $arguments)
    {
        $methodsWithStatesArray = explode('Of', $methodName);
        if (0 === count($methodsWithStatesArray)) {
            //No specific state required, browse all enable state to find the method
            foreach ($this->_activesStates as $activeStateObject) {
                if (true === $activeStateObject->testMethod($methodName)) {
                    //Method found, extract it
                    $callingClosure = $activeStateObject->getClosure($methodName, $this);
                    //Change current injection
                    $previousClosure = $this->_currentInjectionClosure;
                    $this->_currentInjectionClosure = $callingClosure;

                    //call it
                    try {
                        $returnValues = call_user_func_array($callingClosure, $arguments);
                    } catch(\Exception $e) {
                        //Restore previous closure
                        $this->_currentInjectionClosure = $previousClosure;
                        throw $e;
                    }

                    //Restore previous closure
                    $this->_currentInjectionClosure = $previousClosure;
                    return $returnValues;
                }
            }

            throw new Exception\MethodNotImplemented('Method "'.$methodName.'" is not available with actives states');
        } else {
            //A specific state is required for this call
            $statesName = array_pop($methodsWithStatesArray);
            if (!isset($this->_activesStates[$statesName])) {
                throw new Exception\UnavailableState('Error, the state "'.$statesName.'" is not currently available');
            }

            //Get the state name
            $methodName = implode('Of', $methodsWithStatesArray);

            $activeStateObject = $this->_activesStates[$statesName];
            if (true === $activeStateObject->testMethod($methodName)) {
                //Method found, extract it
                $callingClosure = $activeStateObject->getClosure($methodName, $this);
                //Change current injection
                $previousClosure = $this->_currentInjectionClosure;
                $this->_currentInjectionClosure = $callingClosure;

                //Call it
                try {
                    $returnValues = call_user_func_array($callingClosure, $arguments);
                } catch(\Exception $e) {
                    //Restore previous closure
                    $this->_currentInjectionClosure = $previousClosure;
                    throw $e;
                }

                //Restore previous closure
                $this->_currentInjectionClosure = $previousClosure;
                return $returnValues;
            }

            throw new Exception\MethodNotImplemented('Method "'.$methodName.'" is not available for the state "'.$statesName.'"');
        }
    }

    /**
     * Initialize the proxy
     */
    public function __construct()
    {
        //Initialize internal vars
        $this->_states = new \ArrayObject();
        $this->_activesStates = new \ArrayObject();
    }

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * Return a stable unique id of the current object. It must identify
     * @return string
     */
    public function getObjectUniqueId()
    {
        if (null === $this->_uniqueId) {
            //Generate the unique Id
            $this->_uniqueId = uniqid(sha1(microtime(true)), true);
        }

        return $this->_uniqueId;
    }

    /**
     * Called to clone an Object
     * @return $this
     */
    public function __clone()
    {
        //Clone states stack
        $clonedStatesArray = new \ArrayObject();
        foreach ($this->_states as $state) {
            //Clone each state object
            $clonedState = clone $state;
            //Update new stack
            $clonedStatesArray[] = $clonedState;
        }

        //Enabling states
        $activesStates = array_keys($this->_activesStates->getArrayCopy());
        $this->_activesStates = $clonedStatesArray;
        foreach ($activesStates as $stateName) {
            $this->enableState($stateName);
        }

        return $this;
    }

    /***********************
     *** States Management *
     ***********************/

    /**
     * Register dynamically a new state for this object
     * @param string $stateName
     * @param States\States\StateInterface $stateObject
     * @return $this
     */
    public function registerState($stateName, States\States\StateInterface $stateObject)
    {
        $this->_states[$stateName] = $stateObject;

        return $this;
    }

    /**
     * Remove dynamically a state from this object
     * @param string $stateName
     * @return $this
     */
    public function unregisterState($stateName)
    {
        if (isset($this->_states[$stateName])) {
            unset($this->_states[$stateName]);
        }

        return $this;
    }

    /**
     * Disable all actives states and load the required states
     * @param string $stateName
     * @return $this
     */
    public function switchState($stateName)
    {
        $this->disableAllStates();
        $this->enableState($stateName);

        return $this;
    }

    /**
     * Enable a loaded states
     * @param $stateName
     * @return $this
     * @throws Exception\StateNotFound if $stateName does not exist
     */
    public function enableState($stateName)
    {
        if (isset($this->_states[$stateName])) {
            $this->_activesStates[$stateName] = $this->_states[$stateName];
        } else {
            throw new Exception\StateNotFound('State "'.$stateName.'" is not available');
        }

        return $this;
    }

    /**
     * Disable an active state (not available for calling, but already loaded)
     * @param string $stateName
     * @return $this
     */
    public function disableState($stateName)
    {
        if (isset($this->_activesStates[$stateName])) {
            unset($this->_activesStates[$stateName]);
        }

        return $this;
    }

    /**
     * Disable all actives states
     * @return $this
     */
    public function disableAllStates()
    {
        $this->_activesStates = new \ArrayObject();
        return $this;
    }

    /**
     * List all available states for this object. Include added dynamically states, exclude removed dynamically states
     * @return string[]
     */
    public function listAvailableStates()
    {
        return array_keys($this->_states->getArrayCopy());
    }

    /**
     * List all enable states for this object.
     * @return string[]
     */
    public function listActivesStates()
    {
        return array_keys($this->_activesStates->getArrayCopy());
    }

    /**
     * Return the current injection closure object to access to its static properties
     * @return DI\InjectionClosureInterface
     * @throws Exception\UnavailableClosure
     */
    public function getStatic()
    {
        if (!$this->_currentInjectionClosure instanceof DI\InjectionClosureInterface) {
            throw new Exception\UnavailableClosure('Error, there a no active closure currently into the proxy');
        }

        return $this->_currentInjectionClosure;
    }

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
    public function __call($name, $arguments)
    {
        return $this->_callThroughState($name, $arguments);
    }

    /**
     * Return the description of the method
     * @param string $methodName
     * @param string $stateName : Return the description for a specific state of the object, if null, use the current state
     * @return \ReflectionMethod
     * @throws Exception\StateNotFound is the state required is not available
     */
    public function getMethodDescription($methodName, $stateName = null)
    {
        if (null === $stateName) {
            //Browse all state to find the method
            foreach ($this->_states as $stateObject) {
                if ($stateObject->testMethod($methodName)) {
                    return $stateObject->getMethodDescription($methodName);
                }
            }
        }

        if (isset($this->_states[$stateName])) {
            //Retrieve description from the required state
            if ($this->_states[$stateName]->testMethod($methodName)) {
                return $this->_states[$stateName]->getMethodDescription($methodName);
            }
        }

        throw new Exception\StateNotFound('State "'.$stateName.'" is not available');
    }

    /**
     * To invoke an object as a function
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __invoke()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }


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
    public function __get($name)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Test if a property is set for the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __isset($name)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Update a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @param string $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __set($name, $value)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * To remove a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __unset($name)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * To transform the object to a string
     * Hooks and event are automatically called
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function __toString()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return int
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function count()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @param string|int $offset
     * @return bool
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetExists($offset)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @param string|int $offset
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetGet($offset)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Assigns a value to the specified offset.
     * @param string|int $offset
     * @param mixed $value
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetSet($offset, $value)
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Unsets an offset.
     * @param string|int $offset
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function offsetUnset($offset)
    {
        $this->_callThroughState(__METHOD__, func_get_args());
    }

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function current()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns the key of the current element.
     * @return mixed
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function key()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Moves the current position to the next element.
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function next()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Rewinds back to the first element of the Iterator.
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function rewind()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Seeks to a given position in the iterator.
     * @param int $position
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function seek($position)
    {
        $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @return bool
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function valid()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns an external iterator.
     * @return \Traversable
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function getIterator()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

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
    public function serialize()
    {
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * To wake up the object
     * Hooks and event are automatically called
     * @param string $serialized
     * @throws Exception\MethodNotImplemented if any enable state implement the required method
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function unserialize($serialized)
    {
        $this->_callThroughState(__METHOD__, func_get_args());
    }
}