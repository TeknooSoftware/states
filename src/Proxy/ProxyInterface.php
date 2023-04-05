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

namespace Teknoo\States\Proxy;

use Teknoo\States\State\Exception\InvalidArgument;
use Teknoo\States\State\StateInterface;

/**
 * Interface to define proxies classes in stated classes. It represent the main class to instantiate.
 *
 * The proxy, by default, redirect all calls, of non defined methods in the proxy, to enabled states.
 * $this, static and self keywords in all methods the stated class instance (aka in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. Since States 3.0 has no factories or no loader : proxies embedded directly theirs
 * states' configurations. Proxy's implementations manage directly this auto-configuration.
 *
 * States can be overload by children of a stated class : The overloading uses only the non qualified name.
 *
 * Since 3.0, states's methods are a builder, returning a real closure to use. The state must not use
 * the Reflection API to extract the closure (`Closure` from `Reflection` are not bindable on a new scope since 7.1).
 * States can be also an anonymous class, it's name must be defined by an interface, implementing by this state.
 *
 * Since 3.2, the library following #east programming rules, all methods designed to know the state of the object are
 * been removed. Closures are now bound (with the proxy) and called by states's managing object and not directly by
 * the proxy. Result are then injected into proxy. This behavior allows developers to call several methods before return
 * the result. (But only one result is granted).
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface ProxyInterface
{
    /*
     * Name of the default state to load automatically in the construction.
     */
    final public const DEFAULT_STATE_NAME = 'StateDefault';

    /***********************
     *** States Management *
     ***********************/

    /**
     * To register dynamically a new state for this stated class instance. The stateName must be a valid full qualified
     * class name or a valid full qualified interface name. $stateObject must implements, inherits or instantiate the
     * class name passed by $stateName, so $stateObject can be an anonymous class.
     *
     * @api
     *
     * @throws Exception\IllegalName when the identifier is not a valid full qualified class/interface  name
     * @throws Exception\IllegalName when the $stateObject does not implement $stateName
     */
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface;

    /**
     * To remove dynamically a state from this stated class instance. The stateName must be a valid full qualified class
     * name or a valid full qualified interface name.
     *
     * @api
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     */
    public function unregisterState(string $stateName): ProxyInterface;

    /**
     * To disable all enabled states and enable the required states. The stateName must be a valid full qualified class
     * name or a valid full qualified interface name.
     *
     * @api
     *
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     * @throws Exception\StateNotFound if $stateName does not exist
     */
    public function switchState(string $stateName): ProxyInterface;

    /**
     * To enable a loaded states. The stateName must be a valid full qualified class name or a valid full qualified
     * interface name.
     *
     * @api
     *
     * @throws Exception\StateNotFound if $stateName does not exist
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     */
    public function enableState(string $stateName): ProxyInterface;

    /**
     * To disable an enabled state. The stateName must be a valid full qualified class name or a valid full qualified
     * interface name.
     *
     * @api
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     */
    public function disableState(string $stateName): ProxyInterface;

    /**
     * To disable all actives states.
     *
     * @api
     */
    public function disableAllStates(): ProxyInterface;

    /**
     * Check if this stated class instance is in the required state defined by $stateName and call the callback function
     * if it's true. The list of actives states (array of string) is passed to the callback
     *
     * @api
     *
     * @param array<int|string, class-string> $statesNames
     * @throws Exception\IllegalName when the identifier is not a valid full qualified class/interface  name
     */
    public function isInState(array $statesNames, callable $callback, bool $allRequired = false): ProxyInterface;

    /**
     * Check if this stated class instance is not in the required state defined by $stateName and call the callback
     * function if it's true. The list of actives states (array of string) is passed to the callback
     *
     * @api
     *
     * @param array<int|string, class-string> $statesNames
     * @throws Exception\IllegalName when the identifier is not a valid full qualified class/interface  name
     */
    public function isNotInState(array $statesNames, callable $callback, bool $allForbidden = false): ProxyInterface;

    /*******************
     * Methods Calling *
     *******************/

    /**
     * To catch all non defined methods in the proxy to forward it to an enable state of this stated class.
     *
     * @api
     *
     * @param array<int|string, mixed> $arguments
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws InvalidArgument                when the method name is not a string
     */
    public function __call(string $name, array $arguments): mixed;
}
