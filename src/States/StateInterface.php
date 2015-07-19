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

namespace UniAlteri\States\States;

use UniAlteri\States\DI;

/**
 * Interface StateInterface
 * Interface to define a state for a stated class. Each state must implement this interface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
     * To register a DI container for this object.
     *
     * @api
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container): StateInterface;

    /**
     * To return the DI Container used for this object.
     *
     * @api
     * @return DI\ContainerInterface
     */
    public function getDIContainer(): DI\ContainerInterface;

    /**
     * To get the canonical stated class name associated to this state.
     *
     * @internal
     * @return string
     */
    public function getStatedClassName(): string;

    /**
     * To set the canonical stated class name associated to this state.
     *
     * @internal
     * @param string $statedClassName
     *
     * @return $this
     */
    public function setStatedClassName(string $statedClassName): StateInterface;

    /**
     * To know if the mode Private is enabled : private method are only accessible from
     * method present in the same stated class and not from methods of children of this class.
     * By default this mode is disable.
     *
     * @internal
     * @return bool
     */
    public function isPrivateMode(): bool;

    /**
     * To enable or disable the private mode of this state :
     * If the mode Private is enable, private method are only accessible from
     * method present in the same stated class and not from methods of children of this class.
     * By default this mode is disable.
     * @internal
     * @param bool $enable
     *
     * @return
     */
    public function setPrivateMode(bool $enable): StateInterface;

    /**
     * To return an array of string listing all methods available in the state.
     *
     * @api
     * @return string[]
     */
    public function listMethods();

    /**
     * To test if a method exists for this state in the current visibility scope.
     *
     * @internal
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
        string $scope = self::VISIBILITY_PUBLIC,
        string $statedClassOriginName = null
    ): bool;

    /**
     * To return the description of a method to configure the behavior of the proxy. Return also description of private
     * methods.
     *
     * @api
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws Exception\InvalidArgument      when the method name is not a string
     */
    public function getMethodDescription(string $methodName): \ReflectionMethod;

    /**
     * To return a closure of the required method to use in the proxy, according with the current visibility scope.
     *
     * @internal
     * @param string               $methodName
     * @param string               $scope                 self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @param string|null          $statedClassOriginName
     *
     * @return \Closure
     *
     * @throws Exception\MethodNotImplemented is the method does not exist or not available in this scope
     */
    public function getClosure(
        string $methodName,
        string $scope = self::VISIBILITY_PUBLIC,
        string $statedClassOriginName = null
    ): \Closure;
}
