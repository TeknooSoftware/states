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
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;
use \UniAlteri\States\States;
use \UniAlteri\States\States\Exception;

class VirtualState implements States\StateInterface
{
    /**
     * To allow always tested method or not
     * @var bool
     */
    protected $_methodAllowed = false;

    /**
     * To simulate a failure of the getMethodDescription, return an exception method not implemented, but testMethod return true..
     * @var bool
     */
    protected $_simulateMethodDescriptionFailure = false;

    /**
     * To check if a method has been called or not
     * @var bool
     */
    protected $_methodCalled = false;

    /**
     * Fake closure to test method calling
     * @var DI\InjectionClosureInterface
     */
    protected $_closure = null;

    /**
     * Argument used in the call of closure
     * @var array
     */
    protected $_calledArguments = null;

    /**
     * Return the method name called
     * @var string
     */
    protected $_methodName = null;

    /**
     * @var VirtualInjectionClosure
     */
    protected $_virtualInjection = null;

    /**
     * Initialize virtual state
     */
    public function __construct($closure=null)
    {
        if($closure instanceof \Closure){
            $this->_closure = $closure;
        } else {
            $state = $this;
            $this->_closure = $closure = function () use ($state) {
                $state->setMethodCalled();
                $state->setCalledArguments(func_get_args());
                return '';
            };
        }
    }


    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
    }

    /**
     * To allow all call of testMethod and getClosure and return a fake closure
     */
    public function allowMethod()
    {
        $this->_methodAllowed = true;
    }

    /**
     * To simulate a failure of the getMethodDescription, return an exception method not implemented, but testMethod return true..
     */
    public function simulateFailureInGetMethodDescription(){
        $this->_simulateMethodDescriptionFailure = true;
    }

    /**
     * To forbid all call of testMethod and getClosure and return a fake closure
     */
    public function disallowMethod()
    {
        $this->_methodAllowed = false;
    }

    /**
     * Return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods()
    {
        return array();
    }

    /**
     * Test if a method exist for this state
     * @param string $methodName
     * @return boolean
     */
    public function testMethod($methodName)
    {
        return $this->_methodAllowed;
    }

    /**
     * Return the description of a method to configure the behavior of the proxy
     * @param string $methodName
     * @return \ReflectionMethod
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getMethodDescription($methodName)
    {
        if(false === $this->_methodAllowed || true === $this->_simulateMethodDescriptionFailure){
            throw new Exception\MethodNotImplemented();
        }

        $classReflection = new \ReflectionClass($this);
        return $classReflection->getMethod('testMethod');
    }

    /**
     * Return a closure of the required method to use in the proxy
     * @param string $methodName
     * @param Proxy\ProxyInterface $proxy
     * @return DI\InjectionClosureInterface
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getClosure($methodName, $proxy)
    {
        if(false === $this->_methodAllowed){
            throw new Exception\MethodNotImplemented();
        }

        if (null === $this->_virtualInjection) {
            $this->_methodName = $methodName;
            $this->_closure = \Closure::bind($this->_closure, $proxy, get_class($proxy));
            $injection = new VirtualInjectionClosure();
            $injection->setClosure($this->_closure);
            $this->_virtualInjection = $injection;
        }

        return $this->_virtualInjection;
    }

    /**
     * Check if a method has been called
     */
    public function methodWasCalled()
    {
        $value = $this->_methodCalled;
        $this->_methodCalled = false;
        return $value;
    }

    /**
     * Register into the state the argument used for the closure
     * @param array $arguments
     */
    public function setCalledArguments($arguments){
        $this->_calledArguments = $arguments;
    }

    /**
     * Return arguments used for the closure
     * @return array
     */
    public function getCalledArguments(){
        $arguments = $this->_calledArguments;
        $this->_calledArguments = null;
        return $arguments;
    }

    /**
     * Remember that the closure has been called
     */
    public function setMethodCalled(){
        $this->_methodCalled = true;
    }

    /**
     * Return the called method name
     * @return string
     */
    public function getMethodNameCalled()
    {
        $methodName = $this->_methodName;
        $this->_methodName = null;
        return $methodName;
    }
}