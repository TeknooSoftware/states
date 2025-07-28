<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Proxy;

use PHPUnit\Framework\Error\Warning;
use Teknoo\States\Proxy;
use Teknoo\States\Proxy\Exception;
use Teknoo\States\State\StateInterface;
use Teknoo\Tests\Support\MockState1;
use Teknoo\Tests\Support\MockState2;
use Teknoo\Tests\Support\MockState3;

use function class_exists;
use function set_error_handler;

use const E_WARNING;

/**
 * Class AbstractProxyTests
 * Abstract tests case to test the excepted behavior of each proxy implementing the interface
 * Proxy\ProxyInterface.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractProxyTests extends \PHPUnit\Framework\TestCase
{
    /**
     * Mock state 1, used in these tests.
     *
     * @var MockState1
     */
    protected $state1;

    /**
     * Mock state 2, used in these tests.
     *
     * @var MockState2
     */
    protected $state2;

    /**
     * Mock state 3, used in these tests.
     *
     * @var MockState3
     */
    protected $state3;

    /**
     * Proxy to test and validate.
     *
     * @var Proxy\ProxyInterface|Proxy\MagicCallTrait
     */
    protected $proxy;

    /**
     * Initialize objects for tests.
     */
    protected function setUp(): void
    {
        $this->state1 = new MockState1(false, 'my\Stated\Class');
        $this->state2 = new MockState2(false, 'my\Stated\Class');
        $this->state3 = new MockState3(false, 'my\Stated\Class');
        $this->buildProxy();
    }

    protected function tearDown(): void
    {
        $this->proxy = null;
    }

    /**
     * Build a proxy object, into $this->proxy to test it.
     *
     * @return Proxy\ProxyInterface
     */
    abstract protected function buildProxy();

    /**
     * Initialize proxy for test, register all states and enable one it.
     *
     * @param string $stateToEnable         to enable automatically into proxy
     * @param bool   $allowingMethodCalling : if state must
     */
    protected function initializeStateProxy($stateToEnable = null, $allowingMethodCalling = false)
    {
        if (empty($stateToEnable)) {
            $stateToEnable = MockState1::class;
        }

        $this->proxy->registerState(MockState1::class, $this->state1);
        $this->proxy->registerState(MockState2::class, $this->state2);
        $this->proxy->registerState(MockState3::class, $this->state3);
        $this->proxy->enableState($stateToEnable);

        switch ($stateToEnable) {
            case MockState1::class:
                if (true === $allowingMethodCalling) {
                    $this->state1->allowMethod();
                } else {
                    $this->state1->disallowMethod();
                }

                break;
            case MockState2::class:
                if (true === $allowingMethodCalling) {
                    $this->state2->allowMethod();
                } else {
                    $this->state2->disallowMethod();
                }

                break;
            case MockState3::class:
                if (true === $allowingMethodCalling) {
                    $this->state3->allowMethod();
                } else {
                    $this->state3->disallowMethod();
                }

                break;
        }
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     *
     */
    public function testExceptionOnRegisterAStateWithInvalidName(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->registerState([], $this->state1);
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     *
     */
    public function testExceptionOnRegisterAStateWithAnEmptyName(): void
    {
        $this->expectException(Exception\IllegalName::class);
        $this->proxy->registerState('', $this->state1);
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     */
    public function testExceptionOnRegisterAStateWithANonExistentClassName(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->registerState('fooBar', $this->state1);
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     */
    public function testExceptionOnRegisterAStateWithAClassNameNotImplementingTheState(): void
    {
        $this->expectException(Exception\IllegalName::class);
        $this->proxy->registerState(\DateTime::class, $this->state1);
    }

    /**
     * Check behavior of the proxy when we add a new state.
     */
    public function testRegisterStateWithInterface(): void
    {
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->registerState(StateInterface::class, $this->state1)
        );
    }

    /**
     * Check behavior of the proxy when we add a new state.
     */
    public function testRegisterStateWithCanonicalName(): void
    {
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->registerState(MockState1::class, $this->state1)
        );
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     */
    public function testExceptionOnUnRegisterAStateWithInvalidString(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->unregisterState([]);
    }

    /**
     * Proxy must throw an exception if the state to remove is not registered.
     */
    public function testExceptionOnUnRegisterAStateWithNonExistentClass(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->unregisterState('NonExistentState');
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     */
    public function testExceptionOnUnRegisterAStateWithNonRegisteredState(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->unregisterState(\DateTime::class);
    }

    /**
     * Test proxy behavior to unregister a state.
     */
    public function testUnRegisterState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->unregisterState(MockState2::class)
        );
    }

    /**
     * Test proxy behavior to unregister an active state.
     */
    public function testUnRegisterAnEnabledState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->enableState(MockState3::class)
        );

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->unregisterState(MockState3::class)
        );
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string in switch state method.
     */
    public function testExceptionOnSwitchOnStateInvalidString(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->switchState([]);
    }

    /**
     * Proxy must throw an exception if the state does not exist in switch state method.
     */
    public function testExceptionOnSwitchToNonRegisteredState(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->switchState('NonExistentState');
    }

    /**
     * Test proxy behavior when we switch of states.
     */
    public function testSwitchState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->switchState(MockState3::class)
        );
    }

    /**
     * Test proxy behavior when we switch to already enable state.
     */
    public function testSwitchToAnAlreadyEnabledState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->enableState(MockState2::class)
        );
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->switchState(MockState2::class)
        );
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want enable a state.
     */
    public function testExceptionOnEnableStateWithInvalidString(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->enableState([]);
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     */
    public function testExceptionOnEnableStateWithNonRegisteredState(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->enableState(\DateTime::class);
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state.
     */
    public function testExceptionOnEnableStateWithNonExistentClass(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->enableState('NonExistentState');
    }

    /**
     * Check proxy behavior when we enable a state.
     */
    public function testEnableState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(Proxy\ProxyInterface::class, $this->proxy->disableState(MockState1::class));

        $this->assertInstanceOf(Proxy\ProxyInterface::class, $this->proxy->enableState(MockState2::class));
    }

    /**
     * Check proxy behavior when we enable multiple states.
     */
    public function testEnableMultipleState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(Proxy\ProxyInterface::class, $this->proxy->enableState(MockState2::class));
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want disable a state.
     */
    public function testExceptionOnDisableStateWithInvalidString(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->disableState([]);
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     */
    public function testExceptionOnDisableStateWithNonRegisteredState(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->disableState(\DateTime::class);
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state.
     */
    public function testExceptionOnDisableStateWithNonExistentClass(): void
    {
        $this->expectException(Exception\StateNotFound::class);
        $this->proxy->disableState('NonExistentState');
    }

    /**
     * Check proxy behavior when we disable a state.
     */
    public function testDisableState(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(Proxy\ProxyInterface::class, $this->proxy->enableState(MockState2::class));

        $this->assertInstanceOf(Proxy\ProxyInterface::class, $this->proxy->disableState(MockState1::class));
    }

    /**
     * Check proxy behavior when we disable multiple states.
     */
    public function testDisableAllStates(): void
    {
        $this->initializeStateProxy();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->enableState(MockState2::class)
        );

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->disableAllStates()
        );
    }

    public function testExceptionOnIsInStateWithInvalidArray(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy()->isInState('', function (): void {
        });
    }

    public function testExceptionOnIsInStateWithInvalidCallable(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy()->isInState(['foo'], []);
    }

    /**
     * Test behavior of the proxy when it was not initialized.
     */
    public function testInStateCallBehaviorOnNonInitialized(): void
    {
        $proxyReflectionClass = new \ReflectionClass($this->buildProxy());
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([\DateTime::class], function (): never {
                self::fail();
            })
        );
    }

    /**
     * Test behavior of the proxy method inState.
     */
    public function testIsInStateCallbackOnActiveState(): void
    {
        /*
         * @var Proxy\ProxyInterface
         */
        $proxy = $this->buildProxy();
        $proxy->registerState(MockState1::class, new MockState1(false, "It/A/StatedClass"));
        $proxy->registerState(MockState2::class, new MockState2(false, "It/A/StatedClass"));
        $proxy->enableState(MockState1::class);

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState2::class], function (): never {
                self::fail();
            })
        );

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            })
        );

        $this->assertTrue($called);
    }

    public function testIsInStateCallbackOnEmptyList(): void
    {
        $proxy = $this->buildProxy();

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([], function (array $statesList) use (&$called): void {
                $called = true;
                $this->assertSame([], $statesList);
            })
        );

        $this->assertTrue($called);
    }

    /**
     * Test behavior of the proxy method inState.
     */
    public function testIsInStateCallbackOnActiveStateWhenAllRequired(): void
    {
        /*
         * @var Proxy\ProxyInterface
         */
        $proxy = $this->buildProxy();
        $proxy->registerState(MockState1::class, new MockState1(false, "It/A/StatedClass"));
        $proxy->registerState(MockState2::class, new MockState2(false, "It/A/StatedClass"));
        $proxy->enableState(MockState1::class);

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState2::class], function (): never {
                self::fail();
            }, true)
        );

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            }, true)
        );

        $this->assertTrue($called);

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState1::class, MockState2::class], function (): never {
                self::fail();
            }, true)
        );

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState2::class, MockState1::class], function (): never {
                self::fail();
            }, true)
        );

        $proxy->enableState(MockState2::class);
        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState1::class, MockState2::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class, MockState2::class], $statesList);
            }, true)
        );

        $this->assertTrue($called);

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isInState([MockState2::class, MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class, MockState2::class], $statesList);
            }, true)
        );

        $this->assertTrue($called);
    }

    public function testExceptionOnIsNotInStateWithInvalidArray(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy()->isNotInState('', function (): void {
        });
    }

    public function testExceptionOnIsNotInStateWithInvalidCallable(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy()->isNotInState(['foo'], []);
    }

    /**
     * Test behavior of the proxy when it was not initialized.
     */
    public function testNotInStateCallBehaviorOnNonInitialized(): void
    {
        $proxyReflectionClass = new \ReflectionClass($this->buildProxy());
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([\DateTime::class], function () use (&$called): void {
                $called = true;
            })
        );

        $this->assertTrue($called);
    }

    /**
     * Test behavior of the proxy method inState.
     */
    public function testIsNotInStateCallbackOnActiveState(): void
    {
        /*
         * @var Proxy\ProxyInterface
         */
        $proxy = $this->buildProxy();
        $proxy->registerState(MockState1::class, new MockState1(false, "It/A/StatedClass"));
        $proxy->registerState(MockState2::class, new MockState2(false, "It/A/StatedClass"));
        $proxy->enableState(MockState1::class);

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([MockState1::class], function (): never {
                self::fail();
            })
        );

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([MockState2::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            })
        );

        $this->assertTrue($called);
    }

    /**
     * Test behavior of the proxy method inState.
     */
    public function testIsNotInStateCallbackOnActiveStateAllForbidden(): void
    {
        /*
         * @var Proxy\ProxyInterface
         */
        $proxy = $this->buildProxy();
        $proxy->registerState(MockState1::class, new MockState1(false, "It/A/StatedClass"));
        $proxy->registerState(MockState2::class, new MockState2(false, "It/A/StatedClass"));
        $proxy->enableState(MockState1::class);

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([MockState1::class], function (): never {
                self::fail();
            }, true)
        );

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([MockState2::class, MockState1::class], function (): never {
                self::fail();
            }, true)
        );

        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([MockState1::class, MockState2::class], function (): never {
                self::fail();
            }, true)
        );
    }

    public function testIsNotInStateCallbackOnEmptyList(): void
    {
        $proxy = $this->buildProxy();

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $proxy->isNotInState([], function (array $statesList) use (&$called): void {
                $called = true;
                $this->assertSame([], $statesList);
            })
        );

        $this->assertTrue($called);
    }

    /**
     * Test proxy behavior when the called method name is not a string.
     */
    public function testExceptionWhenCallAMethodWithAnInvalidString(): void
    {
        $this->expectException(\TypeError::class);
        $this->proxy->__call([], []);
    }

    /**
     * Test proxy behavior when the required method is not implemented in anything active state.
     */
    public function testExceptionOnCallWithAnNotExistentMethod(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->proxy->test();
    }

    /**
     * Test proxy behavior when the required method is implemented in several active state.
     */
    public function testExceptionWhenCallAMethodAvailableOnMultipleEnabledStates(): void
    {
        $this->expectException(Exception\AvailableSeveralMethodImplementations::class);
        $this->initializeStateProxy();
        $this->proxy->enableState(MockState2::class);
        $this->state1->allowMethod();
        $this->state2->allowMethod();

        $this->proxy->test();
    }

    /**
     * Test proxy behavior in a normal calling.
     */
    public function testCall(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->myCustomMethod('foo', 'bar');

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('myCustomMethod', $this->state1->getMethodNameCalled());
        $this->assertSame(AbstractProxyTests::class, $this->state1->getStatedClassOrigin());
        $this->assertSame(['foo', 'bar'], $this->state1->getCalledArguments());
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Following tests check if the proxy respects visibility restriction private/protected/public of called methods//
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check proxy respects visibility restriction on methods from a function for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromFunction(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a private method
        $fail = false;
        try {
            testCallFromFunctionPrivate();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a protected method
        $fail = false;
        try {
            testCallFromFunctionProtected();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a public method
        testCallFromFunctionPublic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromOtherObject(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->protectedMethod();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new \testCallFromOtherObject();
        $object->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame('testCallFromOtherObject', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from an object of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromChildObject(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new $childClassName();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new $childClassName();
        $object->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new $childClassName();
        $object->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object from the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromOtherObjectSameClass(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        /*
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState(MockState1::class, $this->state1);
        $proxy->registerState(MockState2::class, $this->state2);
        $proxy->registerState(MockState3::class, $this->state3);
        $proxy->enableState(MockState1::class);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of private methods
        $proxy2 = new $childClassName();
        $proxy2->privateMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of protected methods
        $proxy2->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of public methods
        $proxy2->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromThis(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        /*
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState(MockState1::class, $this->state1);
        $proxy->registerState(MockState2::class, $this->state2);
        $proxy->registerState(MockState3::class, $this->state3);
        $proxy->enableState(MockState1::class);

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of private methods
        $proxy->privateMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $proxy->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $proxy->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromThisRecall(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        /*
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState(MockState1::class, $this->state1);
        $proxy->registerState(MockState2::class, $this->state2);
        $proxy->registerState(MockState3::class, $this->state3);
        $proxy->enableState(MockState1::class);

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of private methods
        $proxy->recallMethod('privateMethod');
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $proxy->recallMethod('protectedMethod');
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $proxy->recallMethod('publicMethod');
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame($childClassName, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of another class for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticOtherClass(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of private methods
        $fail = false;
        try {
            \testCallFromOtherObject::privateMethodStatic();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of protected methods
        $fail = false;
        try {
            \testCallFromOtherObject::protectedMethodStatic();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of public methods
        \testCallFromOtherObject::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticChildClass(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $childClassName::privateMethodStatic();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticSameClass(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(__DIR__, 2).'/fixtures/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', $this->proxy::class);
        $childClassName = array_pop($classNamePartArray);
        $childClassName .= 'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.$this->proxy::class.'{ use testCallTrait; }';
        eval($code);

        /*
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState(MockState1::class, $this->state1);
        $proxy->registerState(MockState2::class, $this->state2);
        $proxy->registerState(MockState3::class, $this->state3);
        $proxy->enableState(MockState1::class);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of private methods
        $childClassName::privateMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame('', $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromClosure(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a private method
        $fail = false;
        try {
            $closure = fn () => $proxy->privateTest();
            $closure();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a protected method
        $fail = false;
        try {
            $closure = fn () => $proxy->protectedTest();
            $closure();
        } catch (Exception\MethodNotImplemented) {
            $fail = true;
        } catch (\Exception) {
        }

        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a public method
        $closure = fn () => $proxy->publicTest();
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(AbstractProxyTests::class, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure bound with this current object for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromClosureBound(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a private method
        $closureOriginal = fn () => $proxy->privateTest();
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame(AbstractProxyTests::class, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a protected method
        $closureOriginal = fn () => $proxy->protectedTest();
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(AbstractProxyTests::class, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a public method
        $closureOriginal = fn () => $proxy->publicTest();
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(AbstractProxyTests::class, $this->state1->getStatedClassOrigin());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __invoke is not implemented into in actives states.
     */
    public function testInvokeNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $proxy = $this->proxy;
        $proxy();
    }

    /**
     * Test proxy behavior with the magic method __invoke. If the call is forwarded to the active state.
     */
    public function testInvoke(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $proxy = $this->proxy;
        $proxy('foo', 'bar');

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertEquals('invoke', $this->state1->getMethodNameCalled());
        $this->assertEquals(['foo', 'bar'], $this->state1->getCalledArguments());
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testGetIssetSetUnsetPublic(): void
    {
        //Test defined property
        $this->assertEquals('value1', $this->proxy->publicProperty);
        $this->assertTrue(isset($this->proxy->publicProperty));
        $this->proxy->publicProperty = 'value2';
        $this->assertEquals('value2', $this->proxy->publicProperty);
        unset($this->proxy->publicProperty);
        $this->assertFalse(isset($this->proxy->publicProperty));

        //Test missing property
        $this->assertFalse(isset($this->proxy->missingPublicProperty));
        $fail = false;
        $previous = null;
        if (!class_exists(Warning::class)) {
            $previous = set_error_handler(
                function () use (&$fail): void {
                    $fail = true;
                },
                E_WARNING
            );
        }

        try {
            $a = $this->proxy->missingPublicProperty;
        } catch (\Throwable) {
            $fail = true;
        }

        if (!$fail) {
            if (null !== $previous) {
                \restore_error_handler();
            }

            self::fail('Error __get must throw an exception for missing property');
        }

        $this->proxy->missingPublicProperty = 'fooBar';
        $this->assertTrue(isset($this->proxy->missingPublicProperty));
        $this->assertEquals('fooBar', $this->proxy->missingPublicProperty);
        unset($this->proxy->missingPublicProperty);
        $this->assertFalse(isset($this->proxy->missingPublicProperty));

        $fail = false;
        try {
            $a = $this->proxy->missingPublicProperty;
        } catch (\Throwable) {
            $fail = true;
        }

        if (null !== $previous) {
            \restore_error_handler();
        }

        if (!$fail) {
            self::fail('Error __get must throw an exception for missing property');
        }
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testGetIssetSetUnsetPublicByMethod(): void
    {
        //Test defined property
        $this->initializeStateProxy(MockState1::class, true);
        $this->assertEquals('value1', $this->proxy->getPublicProperty());
        $this->assertTrue($this->proxy->issetPublicProperty());
        $this->proxy->setPublicProperty('value2');
        $this->assertEquals('value2', $this->proxy->getPublicProperty());
        $this->proxy->unsetPublicProperty();

        //Test missing property
        $this->assertFalse($this->proxy->issetMissingPublicProperty());
        $fail = false;
        if (!class_exists(Warning::class)) {
            $previous = set_error_handler(
                function () use (&$fail): void {
                    $fail = true;
                },
                E_WARNING
            );
        }

        try {
            $a = $this->proxy->getOnMissingPublicProperty();
        } catch (\Throwable) {
            $fail = true;
        }

        if (!$fail) {
            if (null !== $previous) {
                \restore_error_handler();
            }

            self::fail('Error __get must throw an exception for missing property');

            return;
        }

        $this->proxy->setOnMissingPublicProperty('fooBar');
        $this->assertTrue($this->proxy->issetMissingPublicProperty());
        $this->assertEquals('fooBar', $this->proxy->getOnMissingPublicProperty());
        $this->proxy->unsetOnMissingPublicProperty();
        $this->assertFalse($this->proxy->issetMissingPublicProperty());
        $fail = false;

        try {
            $a = $this->proxy->getOnMissingPublicProperty();
        } catch (\Throwable) {
            $fail = true;
        }

        if (null !== $previous) {
            \restore_error_handler();
        }

        if (!$fail) {
            self::fail('Error __get must throw an exception for missing property');
        }
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testGetProtectedGet(): void
    {
        $this->expectException(\Throwable::class);
        $this->assertEquals('value1', $this->proxy->protectedProperty);
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testIssetProtectedIsset(): void
    {
        $this->assertFalse(isset($this->proxy->protectedProperty));
        $this->assertFalse(isset($this->proxy->missingProtectedProperty));
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testSetProtected(): void
    {
        $this->expectException(\Throwable::class);
        $this->proxy->protectedProperty = 'value2';
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testUnsetProtected(): void
    {
        $this->expectException(\Throwable::class);
        unset($this->proxy->protectedProperty);
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testGetIssetSetUnsetProtectedViaMethod(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->assertEquals('value1', $this->proxy->getProProperty());
        $this->assertTrue($this->proxy->issetProProperty());
        $this->assertFalse($this->proxy->issetMissingProProperty());
        $this->proxy->setProProperty('value2');
        $this->assertEquals('value2', $this->proxy->getProProperty());
        $this->proxy->unsetProProperty();
        $this->assertFalse($this->proxy->issetProProperty());
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testGetPrivateGet(): void
    {
        $this->expectException(\Throwable::class);
        $this->assertEquals('value1', $this->proxy->privateProperty);
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testIssetPrivateIsset(): void
    {
        $this->assertFalse(isset($this->proxy->privateProperty));
        $this->assertFalse(isset($this->proxy->missingPrivateProperty));
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testSetUnsetPrivate(): void
    {
        $this->expectException(\Throwable::class);
        $this->proxy->privateProperty = 'value2';
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testUnsetPrivate(): void
    {
        $this->expectException(\Throwable::class);
        unset($this->proxy->privateProperty);
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallPublicFromOutside(): void
    {
        $this->assertEquals('fooBar', $this->proxy->publicMethodToCall());
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallProtectedFromOutside(): void
    {
        $this->expectException(\Throwable::class);
        $this->proxy->protectedMethodToCall();
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallPrivateFromOutside(): void
    {
        $this->expectException(\Throwable::class);
        $this->proxy->privateMethodToCall();
    }

    /**
     * Test behavior of magic method __call about a protected method during a state's method calling.
     */
    public function testCallPublicFromState(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->assertEquals('fooBar', $this->proxy->callPublicMethod());
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallProtectedFromState(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->assertEquals('fooBar', $this->proxy->callProMethod());
    }

    /**
     * Test exception behavior of the proxy when __toString is not implemented into in actives states.
     */
    public function testToStringNonImplemented(): void
    {
        $this->initializeStateProxy();
        $s = 'error';
        try {
            $s = (string) $this->proxy;
        } catch (\Exception) {
            self::fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __toString is not implemented into in actives states');
        }

        $this->assertSame('', $s);
    }

    /**
     * Test proxy behavior with the magic method __toString. If the call is forwarded to the active state.
     */
    public function testToString(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $s = (string) $this->proxy;

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('toString', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when count is not implemented into in actives states.
     */
    public function testCountNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->count();
    }

    /**
     * Test proxy behavior with the magic method count. If the call is forwarded to the active state.
     */
    public function testCount(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->count();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('count', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetExist is not implemented into in actives states.
     */
    public function testOffsetExistNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $a = isset($this->proxy[2]);
    }

    /**
     * Test proxy behavior with the magic method offsetExist. If the call is forwarded to the active state.
     */
    public function testOffsetExist(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $a = isset($this->proxy[2]);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetExists', $this->state1->getMethodNameCalled());
        $this->assertSame([2], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetGet is not implemented into in actives states.
     */
    public function testOffsetGetNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $value = $this->proxy[2];
    }

    /**
     * Test proxy behavior with the magic method offsetGet. If the call is forwarded to the active state.
     */
    public function testOffsetGet(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy[2];

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetGet', $this->state1->getMethodNameCalled());
        $this->assertSame([2], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetSet is not implemented into in actives states.
     */
    public function testOffsetSetNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy[2] = 'foo';
    }

    /**
     * Test proxy behavior with the magic method offsetSet. If the call is forwarded to the active state.
     */
    public function testOffsetSet(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy[2] = 'foo';

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetSet', $this->state1->getMethodNameCalled());
        $this->assertSame([2, 'foo'], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetUnset is not implemented into in actives states.
     */
    public function testOffsetUnsetNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        unset($this->proxy[2]);
    }

    /**
     * Test proxy behavior with the magic method offsetUnset. If the call is forwarded to the active state.
     */
    public function testOffsetUnset(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        unset($this->proxy[2]);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetUnset', $this->state1->getMethodNameCalled());
        $this->assertSame([2], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when current is not implemented into in actives states.
     */
    public function testCurrentNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->current();
    }

    /**
     * Test proxy behavior with the magic method current. If the call is forwarded to the active state.
     */
    public function testCurrent(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->current();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('current', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when key is not implemented into in actives states.
     */
    public function testKeyNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->key();
    }

    /**
     * Test proxy behavior with the magic method key. If the call is forwarded to the active state.
     */
    public function testKey(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->key();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('key', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when next is not implemented into in actives states.
     */
    public function testNextNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->next();
    }

    /**
     * Test proxy behavior with the magic method next. If the call is forwarded to the active state.
     */
    public function testNext(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->next();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('next', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when rewind is not implemented into in actives states.
     */
    public function testRewindNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->rewind();
    }

    /**
     * Test proxy behavior with the magic method rewind. If the call is forwarded to the active state.
     */
    public function testRewind(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->rewind();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('rewind', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when seek is not implemented into in actives states.
     */
    public function testSeekNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->seek(1);
    }

    /**
     * Test proxy behavior with the magic method seek. If the call is forwarded to the active state.
     */
    public function testSeek(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->seek(2);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('seek', $this->state1->getMethodNameCalled());
        $this->assertSame([2], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when valid is not implemented into in actives states.
     */
    public function testValidNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->valid();
    }

    /**
     * Test proxy behavior with the magic method valid. If the call is forwarded to the active state.
     */
    public function testValid(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->valid();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('valid', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when getIterator is not implemented into in actives states.
     */
    public function testGetIteratorNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        $this->proxy->getIterator();
    }

    /**
     * Test proxy behavior with the magic method getIterator. If the call is forwarded to the active state.
     */
    public function testGetIterator(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $iterator = new \ArrayIterator([1, 2, 3]);
        $this->state1->setClosure(fn (): \ArrayIterator => $iterator);
        $this->assertSame($iterator, $this->proxy->getIterator());
    }

    /**
     * Test exception behavior of the proxy when serialize is not implemented into in actives states.
     */
    public function testSerializeNonImplemented(): void
    {
        $this->expectException(Exception\MethodNotImplemented::class);
        $this->initializeStateProxy();
        serialize($this->proxy);
    }

    /**
     * Test proxy behavior with the magic method serialize. If the call is forwarded to the active state.
     */
    public function testSerialize(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        serialize($this->proxy);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('__serialize', $this->state1->getMethodNameCalled());
        $this->assertSame([], $this->state1->getCalledArguments());
    }

    /**
     * Test the behavior of the proxy when it is cloned :
     * All states must be cloned
     * DI Container must be cloned
     * Registered states must be cloned
     * Active states must be cloned
     * The cloned proxy must has a new unique id.
     */
    public function testCloning(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $clonedProxy = clone $this->proxy;

        //States must be independently
        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->isInState([MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            })
        );

        $this->assertTrue($called);

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $clonedProxy->isInState([MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            })
        );

        $this->assertTrue($called);

        //List must perform independently
        $clonedProxy->switchState(MockState2::class);
        $clonedProxy->unregisterState(MockState3::class);

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $this->proxy->isInState([MockState1::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState1::class], $statesList);
            })
        );

        $this->assertTrue($called);

        $called = false;
        $this->assertInstanceOf(
            Proxy\ProxyInterface::class,
            $clonedProxy->isInState([MockState2::class], function ($statesList) use (&$called): void {
                $called = true;
                $this->assertEquals([MockState2::class], $statesList);
            })
        );

        $this->assertTrue($called);
    }

    /**
     * Test the behavior of the proxy when it is cloned :
     * All states must be cloned
     * DI Container must be cloned
     * Registered states must be cloned
     * Active states must be cloned
     * The cloned proxy must has a new unique id.
     */
    public function testCloningNoninitializeStateProxy(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $reflectionClassProxyObject = new \ReflectionClass($this->proxy);
        $proxyNotInitialized = $reflectionClassProxyObject->newInstanceWithoutConstructor();
        $proxyCloned = clone $proxyNotInitialized;
        $this->assertNotSame($proxyCloned, $proxyNotInitialized);
    }
}
