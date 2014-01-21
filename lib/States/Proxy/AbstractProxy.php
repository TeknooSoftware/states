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
 * to license@centurion-project.org so we can send you a copy immediately.
 *
 * @category    States
 * @package     Proxy
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @package     Proxy
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Proxy;

class AbstractProxy implements ProxyInterface{

    /**
     * @var \UniAlteri\States\DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * @var string
     */
    protected $_uniqueId = null;

    /**
     * @var \UniAlteri\States\States\StateInterface
     */
    protected $_activeState = null;

    /**
     * @var \UniAlteri\States\States\StateInterface[]
     */
    protected $_states = null;

    /**
     * @param string $methodName
     * @param array $arguments
     */
    protected function _callThroughState($methodName, array $arguments){
        $activeState = $this->getActiveState();

        if(false === $activeState->testMethod($methodName)){
            throw new \UniAlteri\States\Exception\MethodNotFound('Methode "'.$methodName.'" is not available for this state');
        }

        return call_user_func_array($activeState->getClosure($methodName, $this), $arguments);
    }

    /**
     * Initialize the proxy
     * @param mixed $params
     */
    public function __construct($params = null){
        //Initialize internal vars
        $this->_states = new \ArrayObject();
    }

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(\UniAlteri\States\DI\ContainerInterface $container){
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_diContainer;
    }

    /**
     * Return a stable unique id of the current object. It must identify
     * @return string
     */
    public function getObjectUniqueId(){
        $this->_uniqueId = uniqid(sha1(microtime(true)), true);
    }

    /**
     * Called to clone an Obejct
     * @return \UniAlteri\States\Object
     */
    public function __clone(){
        //Clone states stack
        $clonedStatesArray = new \ArrayObject();
        foreach($this->_states as $state){
            //Clone each state object
            $clonedState = clone $state;
            //Update active state to cloned state
            if($state === $this->_activeState){
                $this->_activeState = $clonedState;
            }
            //Update new stack
            $clonedStatesArray[] = $clonedState;
        }
    }

    /***********************
     *** States Management *
     ***********************/

    /**
     * Register dynamically a new state for this object
     * @param string $stateName
     * @param \UniAlteri\States\States\StateInterface $stateObject
     */
    public function registerState($stateName, \UniAlteri\States\States\StateInterface $stateObject){
        $this->_states[$stateName] = $stateObject;
    }

    /**
     * Remove dynamically a state from this object
     * @param string $stateName
     */
    public function unregisterState($stateName){
        if(isset($this->_states[$stateName])){
            unset($this->_states[$stateName]);
        }
    }

    /**
     * Return the name of the current state of this object
     * @return \UniAlteri\States\States\StateInterface
     */
    public function getActiveState(){
        if(!$this->_activeState instanceof \UniAlteri\States\States\StateInterface){
            throw new \UniAlteri\States\Exception\IllegalState('There are no active state');
        }

        return $this->_activeState;
    }

    /**
     * Return the name of the current state of this object
     * @param string $stateName
     * @return mixed
     */
    public function switchState($stateName){
        if(isset($this->_states[$stateName])){
            $this->_activeState = $this->_states[$stateName];
        }
        else{
            throw new \UniAlteri\States\Exception\StateNotFound('State "'.$stateName.'" is not available');
        }
    }

    /**
     * List all available states for this object. Include added dynamically states, exclude removed dynamically states
     * @return string[]
     */
    public function listActivesStates(){
        return array_keys($this->_states->getArrayCopy());
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
     */
    public function __call($name, $arguments){
        return $this->_callThroughState($name, $arguments);
    }

    /**
     * Return the desciption of the method
     * @param string $methodName
     * @param string $stateName : Return the description for a specific state of the object, if null, use the current state
     * @return \ReflectionMethod
     */
    public function getMethodDescription($methodName, $stateName = null){
        if(null === $stateName){
            return $this->getActiveState()->getMethodDescription($methodName);
        }

        if(isset($this->_states[$methodName])){
            return $this->_states[$methodName]->getMethodDescription($methodName);
        }

        throw new \UniAlteri\States\Exception\StateNotFound('State "'.$stateName.'" is not available');
    }

    /**
     * To use the object like a foncter
     * @return mixed
     */
    public function __invoke(){
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
     */
    public function __get($name){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Test if a property is setted for the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     */
    public function __isset($name){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Update a propert of the object
     * Hooks and event are automatically called
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function __set($name, $value){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * To remove a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     */
    public function __unset($name){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * TO transform the object to a string
     * Hooks and event are automatically called
     * @return mixed
     */
    public function __toString(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return int
     */
    public function count(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Assigns a value to the specified offset.
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Unsets an offset.
     * @param mixed $offset
     */
    public function offsetUnset($offset){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     * @return mixed
     */
    public function current(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns the key of the current element.
     * @return mixed
     */
    public function key(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Moves the current position to the next element.
     */
    public function next(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Rewinds back to the first element of the Iterator.
     */
    public function rewind(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Seeks to a given position in the iterator.
     * @param int $position
     */
    public function seek($position){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @return bool
     */
    public function valid(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * Returns an external iterator.
     * @return \Traversable
     */
    public function getIterator(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object
     * Hooks and event are automatically called
     */
    public function serialize(){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }

    /**
     * To wakeup the object
     * Hooks and event are automatically called
     * @param string $serialized
     */
    public function unserialize($serialized){
        return $this->_callThroughState(__METHOD__, func_get_args());
    }
}