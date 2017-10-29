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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support;

use Teknoo\States\Proxy;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State;
use Teknoo\States\State\Exception;
use Teknoo\States\State\StateInterface;

/**
 * Class MockState
 * Mock state to check behavior of factory, finder and proxy.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
     * Return the stated class name who own the state
     *
     * @var string
     */
    protected $statedClassOrigin = null;

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
     *
     * @param bool     $privateMode
     * @param string   $statedClassName
     * @param \Closure $closure
     */
    public function __construct(bool $privateMode, string $statedClassName, $closure = null)
    {
        $this->privateModeEnable = $privateMode;
        $this->statedClassName = $statedClassName;
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
     * To forbid all call of testMethod and getClosure and return a fake closure.
     */
    public function disallowMethod()
    {
        $this->methodAllowed = false;
    }

    /**
     * To update the closure to use in this mock.
     *
     * @param \Closure $closure
     *
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
    public function executeClosure(
        ProxyInterface $object ,
        string $methodName ,
        array $arguments ,
        string $requiredScope ,
        string $statedClassOrigin ,
        callable $returnCallback
    )
    {
        $closure = $this->getClosure($methodName, $requiredScope, $statedClassOrigin);

        if ($closure instanceof \Closure) {
            $returnValue = $closure->call($object, ...$arguments);
            $returnCallback($returnValue);
        }

        return $this;
    }

    private function getClosure(
        string $methodName,
        string $scope = StateInterface::VISIBILITY_PUBLIC,
        string $statedClassOriginName = null
    ) {
        if (false === $this->methodAllowed) {
            return null;
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
                    return null;
                }
                break;
            case StateInterface::VISIBILITY_PUBLIC:
                //Can not access to protected and private method.
                if (false !== stripos($methodName, 'private')) {
                    return null;
                }

                if (false !== stripos($methodName, 'protected')) {
                    return null;
                }
                break;
            default:
                //Bad parameter, throws exception
                throw new Exception\InvalidArgument('Error, the visibility scope is not recognized');
                break;
        }

        $this->methodName = $methodName;
        $this->statedClassOrigin = $statedClassOriginName;

        if (method_exists($this, $methodName)) {
            $rm = new \ReflectionMethod($this, $methodName);
            $rm->setAccessible(true);
            $rmcBuilder = $rm->getClosure($this);

            return $rmcBuilder();
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
     * Return the called method name
     * Method added for test to check different behavior in calling method.
     *
     * @return string
     */
    public function getStatedClassOrigin()
    {
        $statedClassOrigin = $this->statedClassOrigin;
        $this->statedClassOrigin = null;

        return $statedClassOrigin;
    }

    public function recallMethod()
    {
        return function ($methodName) {
            return $this->{$methodName}();
        };
    }

    public function getPublicProperty()
    {
        return function () {
            return $this->publicProperty;
        };
    }

    public function issetPublicProperty()
    {
        return function () {
            return isset($this->publicProperty);
        };
    }

    public function issetMissingPublicProperty()
    {
        return function () {
            return isset($this->missingPublicProperty);
        };
    }

    public function getOnMissingPublicProperty()
    {
        return function () {
            return $this->missingPublicProperty;
        };
    }

    public function setOnMissingPublicProperty()
    {
        return function ($value) {
            $this->missingPublicProperty = $value;
        };
    }

    public function unsetOnMissingPublicProperty()
    {
        return function () {
            unset($this->missingPublicProperty);
        };
    }

    public function setPublicProperty()
    {
        return function ($value) {
            $this->publicProperty = $value;
        };
    }

    public function unsetPublicProperty()
    {
        return function () {
            unset($this->publicProperty);
        };
    }

    public function getProProperty()
    {
        return function () {
            return $this->protectedProperty;
        };
    }

    public function issetProProperty()
    {
        return function () {
            return isset($this->protectedProperty);
        };
    }

    public function issetMissingProProperty()
    {
        return function () {
            return isset($this->missingProtectedProperty);
        };
    }

    public function setProProperty()
    {
        return function ($value) {
            $this->protectedProperty = $value;
        };
    }

    public function unsetProProperty()
    {
        return function () {
            unset($this->protectedProperty);
        };
    }

    public function getPriProperty()
    {
        return function () {
            return $this->privateProperty;
        };
    }

    public function issetPriProperty()
    {
        return function () {
            return isset($this->privateProperty);
        };
    }

    public function issetMissingPriProperty()
    {
        return function () {
            return isset($this->missingPrivateProperty);
        };
    }

    public function setPriProperty()
    {
        return function ($value) {
            $this->privateProperty = $value;
        };
    }

    public function unsetPriProperty()
    {
        return function () {
            unset($this->privateProperty);
        };
    }

    public function getChildrenPriProperty()
    {
        return function () {
            return $this->parentPrivateProperty;
        };
    }

    public function issetChildrenPriProperty()
    {
        return function () {
            return isset($this->parentPrivateProperty);
        };
    }

    public function issetChildrenMissingPriProperty()
    {
        return function () {
            return isset($this->missingPrivateProperty);
        };
    }

    public function setChildrenPriProperty()
    {
        return function ($value) {
            $this->parentPrivateProperty = $value;
        };
    }

    public function unsetChildrenPriProperty()
    {
        return function () {
            unset($this->parentPrivateProperty);
        };
    }

    /**
     * @return string
     */
    public function callPublicMethod()
    {
        return function () {
            return $this->publicMethodToCall();
        };
    }

    /**
     * @return string
     */
    public function callProMethod()
    {
        return function () {
            return $this->protectedMethodToCall();
        };
    }

    /**
     * @return string
     */
    public function callPriMethod()
    {
        return function () {
            return $this->privateMethodToCall();
        };
    }

    /**
     * @return string
     */
    public function callChildrenPriMethod()
    {
        return function () {
            return $this->parentPrivateMethodToCall();
        };
    }
}
