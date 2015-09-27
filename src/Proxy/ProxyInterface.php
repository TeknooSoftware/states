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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Proxy;

use UniAlteri\States\State\StateInterface;

/**
 * Interface ProxyInterface
 * Interface to define proxies classes in stated classes. It is used in this library to create stated class instance.
 *
 * A stated class instance is a proxy instance, configured from the stated class's factory, with different states instance.
 * The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this in all methods of the stated class instance (in proxy's method and states' methods) represent the proxy instance.
 *
 * By default, this library creates an alias with the canonical proxy class name and the stated class name
 * to simulate a real class with the stated class name.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License

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
     * Called to clone this stated class instance, clone states entities and the current state of this instance
     * @api
     *
     * @return $this
     */
    public function __clone();

    /***********************
     *** States Management *
     ***********************/

    /**
     * To register dynamically a new state for this stated class instance.
     * @api
     *
     * @param string                       $stateName
     * @param StateInterface $stateObject
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName     when the identifier is not an non empty string
     */
    public function registerState(\string $stateName, StateInterface $stateObject): ProxyInterface;

    /**
     * To remove dynamically a state from this stated class instance.
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier is not an non empty string
     */
    public function unregisterState(\string $stateName): ProxyInterface;

    /**
     * To disable all enabled states and enable the required states.
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName     when the identifier is not an non empty string
     */
    public function switchState(\string $stateName): ProxyInterface;

    /**
     * To enable a loaded states.
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound   if $stateName does not exist
     * @throws Exception\IllegalName     when the identifier is not an non empty string
     */
    public function enableState(\string $stateName): ProxyInterface;

    /**
     * To disable an enabled state
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier is not an non empty string
     */
    public function disableState(\string $stateName): ProxyInterface;

    /**
     * To disable all actives states.
     * @api
     *
     * @return ProxyInterface
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
     * @return \ArrayAccess|StateInterface[]
     */
    public function getStatesList();

    /**
     * Check if this stated class instance is in the required state defined by $stateName.
     * @api
     *
     * @param string $stateName
     *
     * @return bool
     */
    public function inState(\string $stateName): \bool;

    /*******************
     * Methods Calling *
     *******************/

    /**
     * To call a method of the this stated class instance not defined in the proxy.
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
    public function __call(\string $name, array $arguments);

    /**
     * To return the description of a method present in a state of this stated class instance.
     * This method no checks if the method is available in the current scope by the called
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
    public function getMethodDescription(\string $methodName, \string $stateName = null): \ReflectionMethod;
}
