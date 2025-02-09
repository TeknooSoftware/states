<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
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

    protected bool $privateModeEnable;

    protected string $statedClassName;

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
            $this->closure = $closure = function () use ($state): bool|array|string {
                $state->setMethodCalled();
                $state->setCalledArguments(func_get_args());

                if (str_contains((string) $state->extractMethodCalled(), 'offsetExists')) {
                    return true;
                }

                if (str_contains((string) $state->extractMethodCalled(), 'valid')) {
                    return true;
                }

                if (str_contains((string) $state->extractMethodCalled(), '__serialize')) {
                    return ['foo'];
                }

                return '';
            };
        }
    }

    /**
     * To allow all call of testMethod and getClosure and return a fake closure.
     */
    public function allowMethod(): void
    {
        $this->methodAllowed = true;
    }

    /**
     * To forbid all call of testMethod and getClosure and return a fake closure.
     */
    public function disallowMethod(): void
    {
        $this->methodAllowed = false;
    }

    /**
     * To update the closure to use in this mock.
     *
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
        ProxyInterface $object,
        string &$methodName,
        array &$arguments,
        State\Visibility $requiredScope,
        string &$statedClassOrigin,
        callable &$returnCallback
    ): StateInterface {
        $closure = $this->getClosure($methodName, $requiredScope, $statedClassOrigin);

        if ($closure instanceof \Closure) {
            $returnValue = $closure->call($object, ...$arguments);
            $returnCallback($returnValue);
        }

        return $this;
    }

    private function getClosure(
        string $methodName,
        State\Visibility $scope = State\Visibility::Public,
        ?string $statedClassOriginName = null
    ) {
        if (false === $this->methodAllowed) {
            return null;
        }

        //Simulate real behavior from the name of the method,
        //if the method name contains private, its a private method
        //if the method name contains protected, its a protected method
        //else its a public method
        switch ($scope) {
            case State\Visibility::Private:
                //Private, can access all
                break;
            case State\Visibility::Protected:
                //Can not access to private methods
                if (false !== stripos($methodName, 'private')) {
                    return null;
                }
                break;
            case State\Visibility::Public:
                //Can not access to protected and private method.
                if (false !== stripos($methodName, 'private')) {
                    return null;
                }

                if (false !== stripos($methodName, 'protected')) {
                    return null;
                }
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
     */
    public function setCalledArguments(array $arguments): void
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
    public function setMethodCalled(): void
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

    public function extractMethodCalled()
    {
        return $this->methodName;
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
        return fn ($methodName) => $this->{$methodName}();
    }

    public function getPublicProperty()
    {
        return fn () => $this->publicProperty;
    }

    public function issetPublicProperty()
    {
        return fn (): bool => isset($this->publicProperty);
    }

    public function issetMissingPublicProperty()
    {
        return fn (): bool => isset($this->missingPublicProperty);
    }

    public function getOnMissingPublicProperty()
    {
        return fn () => $this->missingPublicProperty;
    }

    public function setOnMissingPublicProperty()
    {
        return function ($value): void {
            $this->missingPublicProperty = $value;
        };
    }

    public function unsetOnMissingPublicProperty()
    {
        return function (): void {
            unset($this->missingPublicProperty);
        };
    }

    public function setPublicProperty()
    {
        return function ($value): void {
            $this->publicProperty = $value;
        };
    }

    public function unsetPublicProperty()
    {
        return function (): void {
            unset($this->publicProperty);
        };
    }

    public function getProProperty()
    {
        return fn () => $this->protectedProperty;
    }

    public function issetProProperty()
    {
        return fn (): bool => isset($this->protectedProperty);
    }

    public function issetMissingProProperty()
    {
        return fn (): bool => isset($this->missingProtectedProperty);
    }

    public function setProProperty()
    {
        return function ($value): void {
            $this->protectedProperty = $value;
        };
    }

    public function unsetProProperty()
    {
        return function (): void {
            unset($this->protectedProperty);
        };
    }

    public function getPriProperty()
    {
        return fn () => $this->privateProperty;
    }

    public function issetPriProperty()
    {
        return fn (): bool => isset($this->privateProperty);
    }

    public function issetMissingPriProperty()
    {
        return fn (): bool => isset($this->missingPrivateProperty);
    }

    public function setPriProperty()
    {
        return function ($value): void {
            $this->privateProperty = $value;
        };
    }

    public function unsetPriProperty()
    {
        return function (): void {
            unset($this->privateProperty);
        };
    }

    public function getChildrenPriProperty()
    {
        return fn () => $this->parentPrivateProperty;
    }

    public function issetChildrenPriProperty()
    {
        return fn (): bool => isset($this->parentPrivateProperty);
    }

    public function issetChildrenMissingPriProperty()
    {
        return fn (): bool => isset($this->missingPrivateProperty);
    }

    public function setChildrenPriProperty()
    {
        return function ($value): void {
            $this->parentPrivateProperty = $value;
        };
    }

    public function unsetChildrenPriProperty()
    {
        return function (): void {
            unset($this->parentPrivateProperty);
        };
    }

    /**
     * @return string
     */
    public function callPublicMethod()
    {
        return fn () => $this->publicMethodToCall();
    }

    /**
     * @return string
     */
    public function callProMethod()
    {
        return fn () => $this->protectedMethodToCall();
    }

    /**
     * @return string
     */
    public function callPriMethod()
    {
        return fn () => $this->privateMethodToCall();
    }

    /**
     * @return string
     */
    public function callChildrenPriMethod()
    {
        return fn () => $this->parentPrivateMethodToCall();
    }
}
