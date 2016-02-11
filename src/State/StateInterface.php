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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\State;

use Teknoo\States\Proxy\ProxyInterface;

/**
 * Interface StateInterface
 * Interface to define class representing states entities in stated class.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface StateInterface
{
    /**
     * Const to get a closure into a public scope.
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * Const to get a closure into a protected scope.
     */
    const VISIBILITY_PROTECTED = 'protected';

    /**
     * Const to get a closure into a private scope.
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * To initialize this state
     * @param bool $privateMode
     * @param string $statedClassName
     * @param string[] $aliases
     */
    public function __construct(bool $privateMode, string $statedClassName, array $aliases=[]);

    /**
     * To get the canonical stated class name associated to this state.
     *
     * @return string
     */
    public function getStatedClassName(): string;

    /**
     * To set the canonical stated class name associated to this state.
     *
     * @param string $statedClassName
     *
     * @return StateInterface
     */
    public function setStatedClassName(string $statedClassName): StateInterface;

    /**
     * To update the list of aliases of this state in the current stated class
     *
     * @param string[] $aliases
     *
     * @return StateInterface
     */
    public function setStateAliases(array $aliases): StateInterface;

    /**
     * Return the list of aliases of this state in the current stated class
     *
     * @return string[]
     */
    public function getStateAliases();

    /**
     * To know if the mode Private is enabled : private method are only accessible from
     * method present in the same stated class and not from methods of children of this class.
     * By default this mode is disable.
     *
     * @return bool
     */
    public function isPrivateMode(): bool;

    /**
     * To enable or disable the private mode of this state :
     * If the mode Private is enable, private method are only accessible from
     * method present in the same stated class and not from methods of children of this class.
     * By default this mode is disable.
     *
     * @param bool $enable
     *
     * @return StateInterface
     */
    public function setPrivateMode(bool $enable): StateInterface;

    /**
     * To return an array of string listing all methods available in the state : public, protected and private.
     * Ignore static method, because there are incompatible with the stated behavior :
     * State can be only applied on instances entities like object,
     * and not on static entities which by nature have no states
     *
     * @api
     * @return string[]
     */
    public function listMethods();

    /**
     * To test if a method exists for this state in the required scope (check from the visibility of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state or another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or another state) and not for its children
     *
     * @param string      $methodName
     * @param string      $scope                 self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @param string|null $statedClassOriginName
     *
     * @return bool
     *
     * @throws Exception\InvalidArgument when the method name is not a string
     */
    public function testMethod(
        string $methodName,
        string $scope = StateInterface::VISIBILITY_PUBLIC,
        string $statedClassOriginName = null
    ): bool;

    /**
     * To return the description of a method to configure the behavior of the proxy. Return also description of private
     * methods : getMethodDescription() does not check if the caller is allowed to call the required method.
     *
     * getMethodDescription() ignores static method, because there are incompatible with the stated behavior :
     * State can be only applied on instances entities like object,
     * and not on static entities which by nature have no states
     *
     * @api
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getMethodDescription(string $methodName): \ReflectionMethod;

    /**
     * To return a closure of the required method to use in the proxy, in the required scope (check from the visibility of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state or another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or another state) and not for its children
     *
     * @param ProxyInterface       $proxy
     * @param string               $methodName
     * @param string               $scope                 self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @param string|null          $statedClassOriginName
     *
     * @return \Closure
     *
     * @throws Exception\MethodNotImplemented is the method does not exist or not available in this scope
     */
    public function getClosure(
        ProxyInterface $proxy,
        string $methodName,
        string $scope = StateInterface::VISIBILITY_PUBLIC,
        string $statedClassOriginName = null
    ): \Closure;
}
