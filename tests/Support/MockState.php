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
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\Proxy;
use UniAlteri\States\State;
use UniAlteri\States\State\Exception;
use UniAlteri\States\State\StateInterface;

/**
 * Class MockState
 * Mock state to check behavior of factory, finder and proxy.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockState implements StateInterface
{
    /**
     * To allow always tested method or not.
     *
     * @var bool
     */
    protected $methodAllowed = false;

    /**
     * To simulate a failure of the getMethodDescription, return an exception method not implemented, but testMethod return true..
     *
     * @var bool
     */
    protected $simulateMethodDescriptionFailure = false;

    /**
     * To check if a method has been called or not.
     *
     * @var bool
     */
    protected $methodCalled = false;

    /**
     * Fake closure to test method calling.
     *
     * @var \Closure
     */
    protected $closure = null;

    /**
     * Argument used in the call of closure.
     *
     * @var array
     */
    protected $calledArguments = null;

    /**
     * Return the method name called.
     *
     * @var string
     */
    protected $methodName = null;

    /**
     * @var \Closure
     */
    protected $virtualInjection = null;

    /**
     * @var bool
     */
    protected $privateModeEnable = false;

    /**
     * @var string
     */
    protected $statedClassName = '';

    /**
     * Initialize virtual state.
     * @param bool $privateMode
     * @param string $statedClassName
     * @param array $aliases
     * @param \Closure $closure
     */
    public function __construct(\bool $privateMode, \string $statedClassName, array $aliases=[], $closure = null)
    {
        $this->setPrivateMode($privateMode)
            ->setStatedClassName($statedClassName);
        if ($closure instanceof \Closure) {
            //Use as testing closure the passed closure
            $this->closure = $closure;
        } else {
            //No testing closure defined, build a default closure, this closure logs in this state all calls
            //Bind $this in another var because $this is not allowed into use()
            $state = $this;
            $this->closure = $closure = function () use ($state) {
                $state->setMethodCalled();
                $state->setCalledArguments(func_get_args());

                return '';
            };
        }
    }

    /**
     * To allow all call of testMethod and getClosure and return a fake closure.
     */
    public function allowMethod()
    {
        $this->methodAllowed = true;
    }

    /**
     * To simulate a failure of the getMethodDescription, return an exception method not implemented, but testMethod return true..
     */
    public function simulateFailureInGetMethodDescription()
    {
        $this->simulateMethodDescriptionFailure = true;
    }

    /**
     * To forbid all call of testMethod and getClosure and return a fake closure.
     */
    public function disallowMethod()
    {
        $this->methodAllowed = false;
    }

    /**
     * {@inheritdoc}
     */
    public function listMethods()
    {
        return array();
    }

    /**
     * To update the closure to use in this mock
     *
     * @param \Closure $closure
     * @return $this
     */
    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function testMethod(
        \string $methodName,
        \string $scope = StateInterface::VISIBILITY_PUBLIC,
        \string $statedClassOriginName = null
    ): \bool {
        //Simulate real behavior from the name of the method,
        //if the method name contains private, its a private method
        //if the method name contains protected, its a protected method
        //else its a public method
        switch ($scope) {
            case StateInterface::VISIBILITY_PRIVATE:
                //Private, can access all
                break;
            case StateInterface::VISIBILITY_PROTECTED:
                //Can not access to private methods
                if (false !== stripos($methodName, 'private')) {
                    return false;
                }
                break;
            case StateInterface::VISIBILITY_PUBLIC:
                //Can not access to protected and private method.
                if (false !== stripos($methodName, 'private')) {
                    return false;
                }

                if (false !== stripos($methodName, 'protected')) {
                    return false;
                }
                break;
            default:
                //Bad parameter, throws exception
                throw new Exception\InvalidArgument('Error, the visibility scope is not recognized');
                break;
        }

        return $this->methodAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription(\string $methodName): \ReflectionMethod
    {
        if (false === $this->methodAllowed || true === $this->simulateMethodDescriptionFailure) {
            throw new Exception\MethodNotImplemented();
        }

        $classReflection = new \ReflectionClass($this);

        return $classReflection->getMethod('testMethod');
    }

    /**
     * {@inheritdoc}
     */
    public function getClosure(
        \string $methodName,
        \string $scope = StateInterface::VISIBILITY_PUBLIC,
        \string $statedClassOriginName = null
    ): \Closure {
        if (false === $this->methodAllowed) {
            throw new Exception\MethodNotImplemented();
        }

        //Simulate real behavior from the name of the method,
        //if the method name contains private, its a private method
        //if the method name contains protected, its a protected method
        //else its a public method
        switch ($scope) {
            case StateInterface::VISIBILITY_PRIVATE:
                //Private, can access all
                break;
            case StateInterface::VISIBILITY_PROTECTED:
                //Can not access to private methods
                if (false !== stripos($methodName, 'private')) {
                    throw new Exception\MethodNotImplemented();
                }
                break;
            case StateInterface::VISIBILITY_PUBLIC:
                //Can not access to protected and private method.
                if (false !== stripos($methodName, 'private')) {
                    throw new Exception\MethodNotImplemented();
                }

                if (false !== stripos($methodName, 'protected')) {
                    throw new Exception\MethodNotImplemented();
                }
                break;
            default:
                //Bad parameter, throws exception
                throw new Exception\InvalidArgument('Error, the visibility scope is not recognized');
                break;
        }

        $this->methodName = $methodName;

        if (method_exists($this, $methodName)) {
            $reflectionObject = new \ReflectionObject($this);
            $reflectionMethod = $reflectionObject->getMethod($methodName);

            return $reflectionMethod->getClosure($this);
        } else {
            return $this->closure;
        }
    }

    /**
     * Check if a method has been called
     * Method added for test to check different behavior in calling method.
     *
     * @return bool
     */
    public function methodWasCalled()
    {
        $value = $this->methodCalled;
        $this->methodCalled = false;

        return $value;
    }

    /**
     * Register into the state the argument used for the closure
     * Method added for test to check different behavior in calling method.
     *
     * @param array $arguments
     */
    public function setCalledArguments($arguments)
    {
        $this->calledArguments = $arguments;
    }

    /**
     * Return arguments used for the closure
     * Method added for test to check different behavior in calling method.
     *
     * @return array
     */
    public function getCalledArguments()
    {
        $arguments = $this->calledArguments;
        $this->calledArguments = null;

        return $arguments;
    }

    /**
     * Remember that the closure has been called
     * Method added for test to check different behavior in calling method.
     */
    public function setMethodCalled()
    {
        $this->methodCalled = true;
    }

    /**
     * Return the called method name
     * Method added for test to check different behavior in calling method.
     *
     * @return string
     */
    public function getMethodNameCalled()
    {
        $methodName = $this->methodName;
        $this->methodName = null;

        return $methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): \string
    {
        return $this->statedClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatedClassName(\string $statedClassName): StateInterface
    {
        $this->statedClassName = $statedClassName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateAliases(array $aliases): StateInterface
    {
        return $this;
    }

    public function getStateAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isPrivateMode(): \bool
    {
        return $this->privateModeEnable;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrivateMode(\bool $enable): StateInterface
    {
        $this->privateModeEnable = !empty($enable);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function recallMethod($methodName)
    {
        return $this->{$methodName}();
    }
}
