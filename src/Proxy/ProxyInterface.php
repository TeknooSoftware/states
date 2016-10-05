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
 */
namespace Teknoo\States\Proxy;

use Teknoo\States\State\Exception\InvalidArgument;
use Teknoo\States\State\StateInterface;

/**
 * Interface ProxyInterface
 * Interface to define proxies classes in stated classes. It is used in this library to create stated class instance.
 *
 * The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this and self keyword in all methods of the stated class instance (in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. States 3.0 has no factories, no loader. Proxies embedded directly theirs states'
 * configurations. Since 3.0, states's methods are a builder, returning a real closure to use. The state does not use
 * the Reflection API to extract the closure (not bindable with new $this since 7.1).
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
     * Called to clone this stated class instance, clone states entities and the current state of this instance.
     *
     * @api
     *
     * @return ProxyInterface
     */
    public function __clone();

    /***********************
     *** States Management *
     ***********************/

    /**
     * List all states's classes available in this state. It's not mandatory to define states of parent's class,
     * They are automatically retrieved by the proxy. Warning, if you redeclare a state of a parent's class, you can
     * access to its private method: this declaration overloads the parent's state.
     *
     * Example:
     * return [
     *  myFirstState::class,
     *  mySecondState::class
     * ];
     *
     * @return array|string[]
     */
    public static function statesListDeclaration(): array;

    /**
     * To register dynamically a new state for this stated class instance. The stateName must be a valid class name
     * or a valid interface name. $stateObject must implements, inherits or instantiate the class name passed by
     * $stateName.
     *
     * @api
     *
     * @param string         $stateName
     * @param StateInterface $stateObject
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName when the identifier is not an non empty string
     * @throws Exception\IllegalName when the $stateObject does not implement $stateName
     */
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface;

    /**
     * To remove dynamically a state from this stated class instance. The stateName must be a valid class name
     * or a valid interface name.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not an non empty string
     */
    public function unregisterState(string $stateName): ProxyInterface;

    /**
     * To disable all enabled states and enable the required states. The stateName must be a valid class name
     * or a valid interface name.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName   when the identifier is not an non empty string
     * @throws Exception\StateNotFound if $stateName does not exist
     */
    public function switchState(string $stateName): ProxyInterface;

    /**
     * To enable a loaded states. The stateName must be a valid class name or a valid interface name.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound if $stateName does not exist
     * @throws Exception\IllegalName   when the identifier is not an non empty string
     */
    public function enableState(string $stateName): ProxyInterface;

    /**
     * To disable an enabled state. The stateName must be a valid class name or a valid interface name.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not an non empty string
     */
    public function disableState(string $stateName): ProxyInterface;

    /**
     * To disable all actives states.
     *
     * @api
     *
     * @return ProxyInterface
     */
    public function disableAllStates(): ProxyInterface;

    /**
     * To list all currently available states for this object. The method must return valid state's class name and not
     * a keyword.
     *
     * @api
     *
     * @return string[]
     */
    public function listAvailableStates(): array;

    /**
     * To list all enable states for this object. The method must return valid state's class name and not
     * a keyword.
     *
     * @api
     *
     * @return string[]
     */
    public function listEnabledStates(): array;

    /**
     * To return the list of all states entity available for this object.
     *
     * @api
     *
     * @return StateInterface[]
     */
    public function getStatesList(): array;

    /**
     * Check if this stated class instance is in the required state defined by $stateName.
     * The stateName must be a valid class name or a valid interface name.
     *
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
     * To catch a non defined method in the proxy to forward it to a enable state of this stated class.
     *
     * @api
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws InvalidArgument                when the method name is not a string
     */
    public function __call(string $name, array $arguments);

    /**
     * To return the description of a method present in a state of this stated class instance.
     * This method checks all available states, not only enable states.
     *
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
}
