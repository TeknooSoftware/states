<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.1
 *
 * Mock factory file to test command for cli helper
 */
namespace Acme\GoodState\States;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;
use UniAlteri\States\States\Exception;
use UniAlteri\States\States\StateInterface;

class StateNormal implements StateInterface
{
    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
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
     * To return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods()
    {
        // TODO: Implement listMethods() method.
    }

    /**
     * To test if a method exists for this state in the current visibility scope.
     * @param  string                    $methodName
     * @param  string                    $scope      self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @return boolean
     * @throws Exception\InvalidArgument when the method name is not a string
     */
    public function testMethod($methodName, $scope = StateInterface::VISIBILITY_PUBLIC)
    {
        // TODO: Implement testMethod() method.
    }

    /**
     * To return the description of a method to configure the behavior of the proxy. Return also description of private
     * methods
     * @param  string                         $methodName
     * @return \ReflectionMethod
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws Exception\InvalidArgument      when the method name is not a string
     */
    public function getMethodDescription($methodName)
    {
        // TODO: Implement getMethodDescription() method.
    }

    /**
     * To return a closure of the required method to use in the proxy, according with the current visibility scope
     * @param  string                         $methodName
     * @param  Proxy\ProxyInterface           $proxy
     * @param  string                         $scope      self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @return DI\InjectionClosureInterface
     * @throws Exception\MethodNotImplemented is the method does not exist or not available in this scope
     * @throws Exception\InvalidArgument      when the method name is not a string
     * @throws Exception\IllegalProxy         when the proxy does not implement the good interface
     * @throws Exception\IllegalService       when there are no DI Container or Injection Closure Container bought
     */
    public function getClosure($methodName, $proxy, $scope = StateInterface::VISIBILITY_PUBLIC)
    {
        // TODO: Implement getClosure() method.
    }
}
