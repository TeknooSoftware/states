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

interface ProxyInterface extends
    \UniAlteri\States\ObjectInterface,
    \Serializable,
    \ArrayAccess,
    \SeekableIterator,
    \IteratorAggregate,
    \Countable
{
    const DefaultProxyName = 'Default';

    /**
     * Initialize the proxy
     * @param null $params
     */
    public function __construct($params = null);

    /***********************
     *** States Management *
     ***********************/

    /**
     * Register dynamically a new state for this object
     * @param string $stateName
     * @param \UniAlteri\States\States\StateInterface $stateObject
     */
    public function registerState($stateName, \UniAlteri\States\States\StateInterface $stateObject);

    /**
     * Remove dynamically a state from this object
     * @param string $stateName
     */
    public function unregisterState($stateName);

    /**
     * Return the name of the current state of this object
     * @return string
     */
    public function getActiveState();

    /**
     * Return the name of the current state of this object
     * @param string $stateName
     * @return mixed
     */
    public function switchState($stateName);

    /**
     * List all available states for this object. Include added dynamically states, exclude removed dynamically states
     * @return string[]
     */
    public function listActivesStates();

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
    public function __call($name, $arguments);

    /**
     * Return the desciption of the method
     * @param string $methodName
     * @param string $stateName : Return the description for a specific state of the object, if null, use the current state
     * @return \ReflectionMethod
     */
    public function getMethodDescription($methodName, $stateName = null);

    /**
     * To use the object like a foncter
     * @return mixed
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
     */
    public function __get($name);

    /**
     * Test if a property is setted for the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     */
    public function __isset($name);

    /**
     * Update a propert of the object
     * Hooks and event are automatically called
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function __set($name, $value);

    /**
     * To remove a property of the object
     * Hooks and event are automatically called
     * @param string $name
     * @return mixed
     */
    public function __unset($name);

    /**
     * TO transform the object to a string
     * Hooks and event are automatically called
     * @return mixed
     */
    public function __toString();

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @return int
     */
    public function count();

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset);

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset);

    /**
     * Assigns a value to the specified offset.
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value);

    /**
     * Unsets an offset.
     * @param mixed $offset
     */
    public function offsetUnset($offset);

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     * @return mixed
     */
    public function current();

    /**
     * Returns the key of the current element.
     * @return mixed
     */
    public function key();

    /**
     * Moves the current position to the next element.
     */
    public function next();

    /**
     * Rewinds back to the first element of the Iterator.
     */
    public function rewind();

    /**
     * Seeks to a given position in the iterator.
     * @param int $position
     */
    public function seek($position);

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     * @return bool
     */
    public function valid();

    /**
     * Returns an external iterator.
     * @return \Traversable
     */
    public function getIterator();

    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object
     * Hooks and event are automatically called
     */
    public function serialize();

    /**
     * To wakeup the object
     * Hooks and event are automatically called
     * @param string $serialized
     */
    public function unserialize($serialized);
}
