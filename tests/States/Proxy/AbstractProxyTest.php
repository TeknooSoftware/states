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
namespace Teknoo\Tests\States\Proxy;

use Teknoo\States\Proxy;
use Teknoo\States\Proxy\Exception;
use Teknoo\Tests\Support\MockState1;
use Teknoo\Tests\Support\MockState2;
use Teknoo\Tests\Support\MockState3;

/**
 * Class AbstractProxyTest
 * Abstract tests case to test the excepted behavior of each proxy implementing the interface
 * Proxy\ProxyInterface.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractProxyTest extends \PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        parent::setUp();
        $this->state1 = new MockState1(false, 'my\Stated\Class');
        $this->state2 = new MockState2(false, 'my\Stated\Class');
        $this->state3 = new MockState3(false, 'my\Stated\Class');
        $this->buildProxy();
    }

    protected function tearDown()
    {
        $this->proxy = null;
        parent::tearDown();
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
    protected function initializeProxy($stateToEnable = null, $allowingMethodCalling = false)
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
     * @expectedException \TypeError
     */
    public function testRegisterStateInvalidName()
    {
        $this->proxy->registerState(array(), $this->state1);
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     */
    public function testRegisterStateBadName()
    {
        try {
            $this->proxy->registerState('', $this->state1);
        } catch (Exception\IllegalName $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalName exception when the stateName does not respect the regex [a-zA-Z][a-zA-Z0-9_\\]+');
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string.
     */
    public function testRegisterStateBadClass()
    {
        try {
            $this->proxy->registerState('fooBar', $this->state1);
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalName exception when the stateName does not respect the regex [a-zA-Z][a-zA-Z0-9_\\]+');
    }

    /**
     * Check behavior of the proxy when we add a new state.
     */
    public function testRegisterState()
    {
        $this->proxy->registerState(MockState1::class, $this->state1);
        $this->assertEquals(array(MockState1::class), $this->proxy->listAvailableStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     *
     * @expectedException \TypeError
     */
    public function testUnRegisterStateInvalidName()
    {
        $this->proxy->unregisterState(array());
    }

    /**
     * Proxy must throw an exception if the state to remove is not registered.
     */
    public function testUnRegisterStateNonExistentState()
    {
        try {
            $this->proxy->unregisterState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     *
     * @expectedException \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function testUnRegisterStateClassExistStateNotFound()
    {
        $this->proxy->unregisterState(\DateTime::class);
    }

    /**
     * Test proxy behavior to unregister a state.
     */
    public function testUnRegisterState()
    {
        $this->initializeProxy();
        $this->proxy->unregisterState(MockState2::class);
        $this->assertEquals(array(MockState1::class, MockState3::class), $this->proxy->listAvailableStates());
    }

    /**
     * Test proxy behavior to unregister an active state.
     */
    public function testUnRegisterEnableState()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState3::class);
        $this->assertEquals(array(MockState1::class, MockState3::class), $this->proxy->listEnabledStates());
        $this->proxy->unregisterState(MockState3::class);
        $this->assertEquals(array(MockState1::class, MockState2::class), $this->proxy->listAvailableStates());
        $this->assertEquals(array(MockState1::class), $this->proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string in switch state method.
     *
     * @expectedException \TypeError
     */
    public function testSwitchStateInvalidName()
    {
        $this->proxy->switchState(array());
    }

    /**
     * Proxy must throw an exception if the state does not exist in switch state method.
     */
    public function testSwitchStateNonExistentName()
    {
        try {
            $this->proxy->switchState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Test proxy behavior when we switch of states.
     */
    public function testSwitchState()
    {
        $this->initializeProxy();
        $this->proxy->switchState(MockState3::class);
        $this->assertEquals(array(MockState3::class), $this->proxy->listEnabledStates());
    }

    /**
     * Test proxy behavior when we switch to already enable state.
     */
    public function testSwitchAlreadyLoadedState()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState2::class);
        $this->proxy->switchState(MockState2::class);
        $this->assertEquals(array(MockState2::class), $this->proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want enable a state.
     *
     * @expectedException \TypeError
     */
    public function testEnableStateInvalidName()
    {
        $this->proxy->enableState(array());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     *
     * @expectedException \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function testEnableStateClassExistStateNotFound()
    {
        $this->proxy->enableState(\DateTime::class);
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state.
     */
    public function testEnableStateNonExistentName()
    {
        try {
            $this->proxy->enableState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Check proxy behavior when we enable a state.
     */
    public function testEnableState()
    {
        $this->initializeProxy();
        $this->proxy->disableState(MockState1::class);
        $this->proxy->enableState(MockState2::class);
        $this->assertEquals(array(MockState2::class), $this->proxy->listEnabledStates());
    }

    /**
     * Check proxy behavior when we enable multiple states.
     */
    public function testEnableMultipleState()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState2::class);
        $this->assertEquals(array(MockState1::class, MockState2::class), $this->proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want disable a state.
     *
     * @expectedException \TypeError
     */
    public function testDisableStateInvalidName()
    {
        $this->proxy->disableState(array());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string.
     *
     * @expectedException \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function testDisableStateClassExistStateNotFound()
    {
        $this->proxy->disableState(\DateTime::class);
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state.
     */
    public function testDisableStateNonExistentName()
    {
        try {
            $this->proxy->disableState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Check proxy behavior when we disable a state.
     */
    public function testDisableState()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState2::class);
        $this->proxy->disableState(MockState1::class);
        $this->assertEquals(array(MockState2::class), $this->proxy->listEnabledStates());
    }

    /**
     * Check proxy behavior when we disable multiple states.
     */
    public function testDisableAllStates()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState2::class);
        $this->proxy->disableAllStates();
        $this->assertEquals(array(), $this->proxy->listEnabledStates());
    }

    /**
     * Check the proxy's method listAvailableStates behavior when there are no registered state.
     */
    public function testListAvailableStatesOfEmpty()
    {
        $this->assertEquals(array(), $this->proxy->listAvailableStates());
    }

    /**
     * Check the proxy's method listAvailableStates behavior when there are no registered state.
     */
    public function testListAvailableStatesNotInit()
    {
        $proxyReflectionClass = new \ReflectionClass($this->proxy);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $this->assertEquals(array(), $proxy->listAvailableStates());
    }

    /**
     * Check the proxy's method listAvailableStates behavior.
     */
    public function testListAvailableStates()
    {
        $this->proxy->registerState(MockState1::class, $this->state1);
        $this->proxy->registerState(MockState3::class, $this->state3);
        $this->assertEquals(array(MockState1::class, MockState3::class), $this->proxy->listAvailableStates());
    }

    /**
     * Check the proxy's method getStatesList behavior when there are no registered state.
     */
    public function testGetStatesListEmpty()
    {
        $this->assertEmpty($this->proxy->getStatesList()->getArrayCopy());
    }

    /**
     * Check the proxy's method getStatesList behavior when there are no registered state.
     */
    public function testGetStatesListNoInit()
    {
        $proxyReflectionClass = new \ReflectionClass($this->proxy);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $this->assertEmpty($proxy->getStatesList()->getArrayCopy());
    }

    /**
     * Check the proxy's method getStatesList behavior.
     */
    public function testGetStatesList()
    {
        $this->proxy->registerState(MockState1::class, $this->state1);
        $this->proxy->registerState(MockState3::class, $this->state3);
        $statesList = $this->proxy->getStatesList();
        $this->assertEquals(2, $statesList->count());
        $this->assertInstanceOf('Teknoo\States\State\StateInterface', $statesList[MockState1::class]);
        $this->assertInstanceOf('Teknoo\States\State\StateInterface', $statesList[MockState3::class]);
    }

    /**
     * Check the proxy's method listEnabledStates behavior when there are no enable state.
     */
    public function testListEnabledStatesNotInit()
    {
        $proxyReflectionClass = new \ReflectionClass($this->proxy);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $this->assertEquals(array(), $proxy->listEnabledStates());
    }

    /**
     * Check the proxy's method listEnabledStates behavior when there are no enable state.
     */
    public function testListEnabledStatesOfEmpty()
    {
        $this->proxy->registerState(MockState1::class, $this->state1);
        $this->proxy->registerState(MockState3::class, $this->state3);
        $this->assertEquals(array(), $this->proxy->listEnabledStates());
    }

    /**
     * Check the proxy's method listEnabledStates behavior.
     */
    public function testListEnabledStates()
    {
        $this->initializeProxy();
        $this->assertEquals(array(MockState1::class), $this->proxy->listEnabledStates());
    }

    /**
     * Test behavior of the proxy when it was not initialized.
     */
    public function testInStateNotInitialized()
    {
        $proxyReflectionClass = new \ReflectionClass($this->buildProxy());
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        $this->assertFalse($proxy->inState(\DateTime::class));
    }

    /**
     * Test behavior of the proxy method inState.
     */
    public function testInState()
    {
        /*
         * @var Proxy\ProxyInterface
         */
        $proxy = $this->getMockObjectGenerator()->getMock(get_class($this->buildProxy()), array('listEnabledStates'), array(), '', false);
        $proxy->expects($this->any())
            ->method('listEnabledStates')
            ->withAnyParameters()
            ->willReturn(array(\DateTime::class, 'Bar'));

        $this->assertFalse($proxy->inState(\stdClass::class));
        $this->assertTrue($proxy->inState(\DateTime::class));
        $proxy = $this->buildProxy();
    }

    /**
     * Test proxy behavior when the called method name is not a string.
     *
     * @expectedException \TypeError
     */
    public function testCallInvalidName()
    {
        $this->proxy->__call(array(), array());
    }

    /**
     * Test proxy behavior when the required method is not implemented in anything active state.
     */
    public function testCallNonImplementedWithoutState()
    {
        try {
            $this->proxy->test();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when no state are available');
    }

    /**
     * Test proxy behavior when the required method is not implemented in the required state.
     */
    public function testCallNonImplementedWithState()
    {
        $this->initializeProxy();
        try {
            $this->proxy->testOfState1();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when no state are available');
    }

    /**
     * Test proxy behavior when the required method is implemented in several active state.
     */
    public function testCallMultipleImplementation()
    {
        $this->initializeProxy();
        $this->proxy->enableState(MockState2::class);
        $this->state1->allowMethod();
        $this->state2->allowMethod();

        try {
            $this->proxy->test();
        } catch (Exception\AvailableSeveralMethodImplementations $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\AvailableSeveralMethodImplementations when there are multiples implementations of a method in several enabled states');
    }

    /**
     * Test proxy behavior in a normal calling.
     */
    public function testCall()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->myCustomMethod('foo', 'bar');

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('myCustomMethod', $this->state1->getMethodNameCalled());
        $this->assertSame(array('foo', 'bar'), $this->state1->getCalledArguments());
    }

    /**
     * Test the proxy behavior when hen we want a description of a non existent method.
     */
    public function testGetMethodDescriptionNonExistentName()
    {
        $this->initializeProxy();
        try {
            $this->proxy->getMethodDescription('NonExistentMethod');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the method was not found');
    }

    /**
     * Test the proxy behavior when hen we want a description of a non existent method in the required state.
     */
    public function testGetMethodDescriptionNonExistentNameByState()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->state1->simulateFailureInGetMethodDescription();
        try {
            $this->proxy->getMethodDescription('NonExistentMethod');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the method was not found');
    }

    /**
     * Test the proxy behavior when the method name is not a valid string when we want its description.
     *
     * @expectedException \TypeError
     */
    public function testGetMethodDescriptionInvalidName()
    {
        $this->proxy->getMethodDescription(array());
    }

    /**
     * Test the proxy behavior when hen we want a description of a method and the required state name is not a string.
     *
     * @expectedException \TypeError
     */
    public function testGetMethodDescriptionInvalidStateName()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->getMethodDescription('method1', array());
    }

    /**
     * Test the proxy behavior when hen we want a description of a method and the required state does not exist.
     * @expectedException \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function testGetMethodDescriptionInvalidState()
    {
        $this->initializeProxy();
        $this->proxy->getMethodDescription('NonExistentMethod', 'NonExistentState');
    }

    /**
     * Test the proxy behavior when hen we want a description of a method and the required state does not exist.
     * @expectedException \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function testGetMethodDescriptionNonExistantState()
    {
        $this->initializeProxy();
        $this->proxy->getMethodDescription('NonExistentMethod', \DateTime::class);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Following tests check the method getMethodDescription in different visibility scope //
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Test getMethodDescription from a function for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromFunction()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a private method
        $this->assertInstanceOf('\ReflectionMethod', testGetMethodDescriptionFromFunctionPrivate());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a protected method
        $this->assertInstanceOf('\ReflectionMethod', testGetMethodDescriptionFromFunctionProtected());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a public method
        $this->assertInstanceOf('\ReflectionMethod', testGetMethodDescriptionFromFunctionPublic());
    }

    /**
     * Test getMethodDescription from another object for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromOtherObject()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $object = new \testGetMethodDescriptionFromOtherObject();
        $this->assertInstanceOf('\ReflectionMethod', $object->privateMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new \testGetMethodDescriptionFromOtherObject();
        $this->assertInstanceOf('\ReflectionMethod', $object->protectedMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new \testGetMethodDescriptionFromOtherObject();
        $this->assertInstanceOf('\ReflectionMethod', $object->publicMethod());
    }

    /**
     * Test getMethodDescription from an object of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromChildObject()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $object = new $childClassName();
        $this->assertInstanceOf('\ReflectionMethod', $object->privateMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new $childClassName();
        $this->assertInstanceOf('\ReflectionMethod', $object->protectedMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new $childClassName();
        $this->assertInstanceOf('\ReflectionMethod', $object->publicMethod());
    }

    /**
     * Test getMethodDescription from another object from the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromOtherObjectSameClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testGetMethodDescriptionTrait; }';
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
        $this->assertInstanceOf('\ReflectionMethod', $proxy2->privateMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of protected methods
        $this->assertInstanceOf('\ReflectionMethod', $proxy2->protectedMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', $proxy2->publicMethod());
    }

    /**
     * Test getMethodDescription from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromThis()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testGetMethodDescriptionTrait; }';
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
        $this->assertInstanceOf('\ReflectionMethod', $proxy->privateMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $this->assertInstanceOf('\ReflectionMethod', $proxy->protectedMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', $proxy->publicMethod());
    }

    /**
     * Test getMethodDescription from a static method of another class for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromStaticOtherClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of private methods
        $this->assertInstanceOf('\ReflectionMethod', \testGetMethodDescriptionFromOtherObject::privateMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of protected methods
        $this->assertInstanceOf('\ReflectionMethod', \testGetMethodDescriptionFromOtherObject::protectedMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', \testGetMethodDescriptionFromOtherObject::publicMethodStatic());
    }

    /**
     * Test getMethodDescription from a static method of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromStaticChildClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::privateMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::protectedMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::publicMethodStatic());
    }

    /**
     * Test getMethodDescription from a static method of the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromStaticSameClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testGetMethodDescriptionTrait; }';
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
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::privateMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of protected methods
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::protectedMethodStatic());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', $childClassName::publicMethodStatic());
    }

    /**
     * Test getMethodDescription from a closure for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromClosure()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a private method
        $closure = function () use ($proxy) {
            return $proxy->getMethodDescription('privateTest');
        };
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a protected method
        $closure = function () use ($proxy) {
            return $proxy->getMethodDescription('protectedTest');
        };
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a public method
        $closure = function () use ($proxy) {
            return $proxy->getMethodDescription('publicTest');
        };
        $this->assertInstanceOf('\ReflectionMethod', $closure());
    }

    /**
     * Test getMethodDescription from a closure bound with this current object for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testGetMethodDescriptionFromClosureBound()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a private method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('privateTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a protected method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('protectedTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a public method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('publicTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());
    }

    /////////////////////////////////////////////////////////////////////////////////
    // End tests for the method getMethodDescription in different visibility scope //
    /////////////////////////////////////////////////////////////////////////////////

    /**
     * Test the proxy behavior when we require a description of a method of a required state.
     */
    public function testGetMethodDescriptionOfState()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->state2->allowMethod();
        $this->assertInstanceOf('\ReflectionMethod', $this->proxy->getMethodDescription('test', MockState2::class));
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
    public function testCallFromFunction()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a private method
        $fail = false;
        try {
            testCallFromFunctionPrivate();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a protected method
        $fail = false;
        try {
            testCallFromFunctionProtected();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a public method
        testCallFromFunctionPublic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromOtherObject()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->protectedMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new \testCallFromOtherObject();
        $object->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from an object of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromChildObject()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new $childClassName();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new $childClassName();
        $object->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new $childClassName();
        $object->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object from the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromOtherObjectSameClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
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
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of protected methods
        $proxy2->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of public methods
        $proxy2->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromThis()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
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
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $proxy->protectedMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $proxy->publicMethod();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromThisRecall()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
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
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $proxy->recallMethod('protectedMethod');
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $proxy->recallMethod('publicMethod');
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of another class for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticOtherClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of private methods
        $fail = false;
        try {
            \testCallFromOtherObject::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of protected methods
        $fail = false;
        try {
            \testCallFromOtherObject::protectedMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of public methods
        \testCallFromOtherObject::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticChildClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->proxy;

        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $childClassName::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromStaticSameClass()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        include_once dirname(dirname(__DIR__)).'/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->proxy).'{ use testCallTrait; }';
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
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists.
     */
    public function testCallFromClosure()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a private method
        $fail = false;
        try {
            $closure = function () use ($proxy) {
                return $proxy->privateTest();
            };
            $closure();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a protected method
        $fail = false;
        try {
            $closure = function () use ($proxy) {
                return $proxy->protectedTest();
            };
            $closure();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a public method
        $closure = function () use ($proxy) {
            return $proxy->publicTest();
        };
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure bound with this current object for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists.
     */
    public function testCallFromClosureBound()
    {
        $this->initializeProxy(MockState1::class, true);
        //To access to the proxy in the method
        $proxy = $this->proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a private method
        $closureOriginal = function () use ($proxy) {
            return $proxy->privateTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('privateTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a protected method
        $closureOriginal = function () use ($proxy) {
            return $proxy->protectedTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a public method
        $closureOriginal = function () use ($proxy) {
            return $proxy->publicTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->proxy);
        $closure();
        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('publicTest', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    ////////////////////////////////////////////////////////////////////////////////////
    // End tests for the method getMethodDescription in different visibility scope    //
    // For all other magics methods : PHP and interface define these methods as public//
    ////////////////////////////////////////////////////////////////////////////////////

    /**
     * Test exception behavior of the proxy when __invoke is not implemented into in actives states.
     */
    public function testInvokeNonImplemented()
    {
        $this->initializeProxy();
        try {
            $proxy = $this->proxy;
            $proxy();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __invoke is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __invoke. If the call is forwarded to the active state.
     */
    public function testInvoke()
    {
        $this->initializeProxy(MockState1::class, true);
        $proxy = $this->proxy;
        $proxy('foo', 'bar');

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertEquals('invoke', $this->state1->getMethodNameCalled());
        $this->assertEquals(array('foo', 'bar'), $this->state1->getCalledArguments());
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testGetIssetSetUnsetPublic()
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
        try {
            $a = $this->proxy->missingPublicProperty;
        } catch (\Throwable $e) {
            $fail = true;
        }
        if (false === $fail) {
            $this->fail('Error __get must throw an exception for missing property');
        }

        $this->proxy->missingPublicProperty = 'fooBar';
        $this->assertTrue(isset($this->proxy->missingPublicProperty));
        $this->assertEquals('fooBar', $this->proxy->missingPublicProperty);
        unset($this->proxy->missingPublicProperty);
        $this->assertFalse(isset($this->proxy->missingPublicProperty));

        $fail = false;
        try {
            $a = $this->proxy->missingPublicProperty;
        } catch (\Throwable $e) {
            $fail = true;
        }
        if (false === $fail) {
            $this->fail('Error __get must throw an exception for missing property');
        }
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testGetIssetSetUnsetPublicByMethod()
    {
        //Test defined property
        $this->initializeProxy(MockState1::class, true);
        $this->assertEquals('value1', $this->proxy->getPublicProperty());
        $this->assertTrue($this->proxy->issetPublicProperty());
        $this->proxy->setPublicProperty('value2');
        $this->assertEquals('value2', $this->proxy->getPublicProperty());
        $this->proxy->unsetPublicProperty();

        //Test missing property
        $this->assertFalse($this->proxy->issetMissingPublicProperty());
        $fail = false;
        try {
            $a = $this->proxy->getOnMissingPublicProperty();
        } catch (\Throwable $e) {
            $fail = true;
        }
        if (false === $fail) {
            $this->fail('Error __get must throw an exception for missing property');
        }

        $this->proxy->setOnMissingPublicProperty('fooBar');
        $this->assertTrue($this->proxy->issetMissingPublicProperty());
        $this->assertEquals('fooBar', $this->proxy->getOnMissingPublicProperty());
        $this->proxy->unsetOnMissingPublicProperty();
        $this->assertFalse($this->proxy->issetMissingPublicProperty());
        $fail = false;
        try {
            $a = $this->proxy->getOnMissingPublicProperty();
        } catch (\Throwable $e) {
            $fail = true;
        }
        if (false === $fail) {
            $this->fail('Error __get must throw an exception for missing property');
        }
    }

    /**
     * Test behavior of magic method during outside calling.
     *
     * @expectedException \Throwable
     */
    public function testGetProtectedGet()
    {
        $this->assertEquals('value1', $this->proxy->protectedProperty);
    }

    /**
     * Test behavior of magic method during a state's method calling.
     */
    public function testIssetProtectedIsset()
    {
        $this->assertFalse(isset($this->proxy->protectedProperty));
        $this->assertFalse(isset($this->proxy->missingProtectedProperty));
    }

    /**
     * Test behavior of magic method during outside calling.
     *
     * @expectedException \Throwable
     */
    public function testSetProtected()
    {
        $this->proxy->protectedProperty = 'value2';
    }

    /**
     * Test behavior of magic method during a state's method calling.
     *
     * @expectedException \Throwable
     */
    public function testUnsetProtected()
    {
        unset($this->proxy->protectedProperty);
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testGetIssetSetUnsetProtectedViaMethod()
    {
        $this->initializeProxy(MockState1::class, true);
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
     *
     * @expectedException \Throwable
     */
    public function testGetPrivateGet()
    {
        $this->assertEquals('value1', $this->proxy->privateProperty);
    }

    /**
     * Test behavior of magic method during outside calling.
     */
    public function testIssetPrivateIsset()
    {
        $this->assertFalse(isset($this->proxy->privateProperty));
        $this->assertFalse(isset($this->proxy->missingPrivateProperty));
    }

    /**
     * Test behavior of magic method during a state's method calling.
     *
     * @expectedException \Throwable
     */
    public function testSetUnsetPrivate()
    {
        $this->proxy->privateProperty = 'value2';
    }

    /**
     * Test behavior of magic method during outside calling.
     *
     * @expectedException \Throwable
     */
    public function testUnsetPrivate()
    {
        unset($this->proxy->privateProperty);
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallPublicFromOutside()
    {
        $this->assertEquals('fooBar', $this->proxy->publicMethodToCall());
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     *
     * @expectedException \Throwable
     */
    public function testCallProtectedFromOutside()
    {
        $this->proxy->protectedMethodToCall();
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     *
     * @expectedException \Throwable
     */
    public function testCallPrivateFromOutside()
    {
        $this->proxy->privateMethodToCall();
    }

    /**
     * Test behavior of magic method __call about a protected method during a state's method calling.
     */
    public function testCallPublicFromState()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->assertEquals('fooBar', $this->proxy->callPublicMethod());
    }

    /**
     * Test behavior of magic method __call about a protected method during outside calling.
     */
    public function testCallProtectedFromState()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->assertEquals('fooBar', $this->proxy->callProMethod());
    }

    /**
     * Test exception behavior of the proxy when __toString is not implemented into in actives states.
     */
    public function testToStringNonImplemented()
    {
        $this->initializeProxy();
        $s = 'error';
        try {
            $s = (string) $this->proxy;
        } catch (\Exception $e) {
            $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __toString is not implemented into in actives states');
        }

        $this->assertEquals('', $s);
    }

    /**
     * Test proxy behavior with the magic method __toString. If the call is forwarded to the active state.
     */
    public function testToString()
    {
        $this->initializeProxy(MockState1::class, true);
        $s = (string) $this->proxy;

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('toString', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when count is not implemented into in actives states.
     */
    public function testCountNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->count();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when count is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method count. If the call is forwarded to the active state.
     */
    public function testCount()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->count();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('count', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetExist is not implemented into in actives states.
     */
    public function testOffsetExistNonImplemented()
    {
        $this->initializeProxy();
        try {
            $a = isset($this->proxy[2]);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetExist is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetExist. If the call is forwarded to the active state.
     */
    public function testOffsetExist()
    {
        $this->initializeProxy(MockState1::class, true);
        $a = isset($this->proxy[2]);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetExists', $this->state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetGet is not implemented into in actives states.
     */
    public function testOffsetGetNonImplemented()
    {
        $this->initializeProxy();
        try {
            $value = $this->proxy[2];
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetGet is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetGet. If the call is forwarded to the active state.
     */
    public function testOffsetGet()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy[2];

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetGet', $this->state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetSet is not implemented into in actives states.
     */
    public function testOffsetSetNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy[2] = 'foo';
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetSet is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetSet. If the call is forwarded to the active state.
     */
    public function testOffsetSet()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy[2] = 'foo';

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetSet', $this->state1->getMethodNameCalled());
        $this->assertSame(array(2, 'foo'), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetUnset is not implemented into in actives states.
     */
    public function testOffsetUnsetNonImplemented()
    {
        $this->initializeProxy();
        try {
            unset($this->proxy[2]);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetUnset is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetUnset. If the call is forwarded to the active state.
     */
    public function testOffsetUnset()
    {
        $this->initializeProxy(MockState1::class, true);
        unset($this->proxy[2]);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('offsetUnset', $this->state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when current is not implemented into in actives states.
     */
    public function testCurrentNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->current();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when current is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method current. If the call is forwarded to the active state.
     */
    public function testCurrent()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->current();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('current', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when key is not implemented into in actives states.
     */
    public function testKeyNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->key();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when key is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method key. If the call is forwarded to the active state.
     */
    public function testKey()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->key();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('key', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when next is not implemented into in actives states.
     */
    public function testNextNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->next();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when next is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method next. If the call is forwarded to the active state.
     */
    public function testNext()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->next();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('next', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when rewind is not implemented into in actives states.
     */
    public function testRewindNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->rewind();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when rewind is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method rewind. If the call is forwarded to the active state.
     */
    public function testRewind()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->rewind();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('rewind', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when seek is not implemented into in actives states.
     */
    public function testSeekNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->seek(1);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when seek is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method seek. If the call is forwarded to the active state.
     */
    public function testSeek()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->seek(2);

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('seek', $this->state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when valid is not implemented into in actives states.
     */
    public function testValidNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->valid();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when valid is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method valid. If the call is forwarded to the active state.
     */
    public function testValid()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->valid();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('valid', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when getIterator is not implemented into in actives states.
     */
    public function testGetIteratorNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->getIterator();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when getIterator is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method getIterator. If the call is forwarded to the active state.
     */
    public function testGetIterator()
    {
        $this->initializeProxy(MockState1::class, true);
        $iterator = new \ArrayIterator([1, 2, 3]);
        $this->state1->setClosure(function () use ($iterator) {
           return $iterator;
        });
        $this->assertSame($iterator, $this->proxy->getIterator());
    }

    /**
     * Test exception behavior of the proxy when serialize is not implemented into in actives states.
     */
    public function testSerializeNonImplemented()
    {
        $this->initializeProxy();
        try {
            serialize($this->proxy);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when serialize is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method serialize. If the call is forwarded to the active state.
     */
    public function testSerialize()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->serialize();

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('serialize', $this->state1->getMethodNameCalled());
        $this->assertSame(array(), $this->state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when unserialize is not implemented into in actives states.
     */
    public function testUnSerializeNonImplemented()
    {
        $this->initializeProxy();
        try {
            $this->proxy->unserialize('');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when unserialize is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method unserialize. If the call is forwarded to the active state.
     */
    public function testUnSerialize()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->unserialize('foo');

        $this->assertTrue($this->state1->methodWasCalled());
        $this->assertSame('unserialize', $this->state1->getMethodNameCalled());
        $this->assertSame(array('foo'), $this->state1->getCalledArguments());
    }

    /**
     * Test the behavior of the proxy when it is cloned :
     * All states must be cloned
     * DI Container must be cloned
     * Registered states must be cloned
     * Active states must be cloned
     * The cloned proxy must has a new unique id.
     */
    public function testCloning()
    {
        $this->initializeProxy(MockState1::class, true);
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $clonedProxy = clone $this->proxy;

        //States must be independently
        $this->assertEquals(array(MockState1::class, MockState2::class, MockState3::class), $this->proxy->listAvailableStates());
        $this->assertEquals(array(MockState1::class), $this->proxy->listEnabledStates());
        $this->assertEquals(array(MockState1::class, MockState2::class, MockState3::class), $clonedProxy->listAvailableStates());
        $this->assertEquals(array(MockState1::class), $clonedProxy->listEnabledStates());

        //List must perform independently
        $clonedProxy->switchState(MockState2::class);
        $clonedProxy->unregisterState(MockState3::class);
        $this->assertEquals(array(MockState1::class, MockState2::class, MockState3::class), $this->proxy->listAvailableStates());
        $this->assertEquals(array(MockState1::class), $this->proxy->listEnabledStates());
        $this->assertEquals(array(MockState1::class, MockState2::class), $clonedProxy->listAvailableStates());
        $this->assertEquals(array(MockState2::class), $clonedProxy->listEnabledStates());
    }

    /**
     * Test the behavior of the proxy when it is cloned :
     * All states must be cloned
     * DI Container must be cloned
     * Registered states must be cloned
     * Active states must be cloned
     * The cloned proxy must has a new unique id.
     */
    public function testCloningNonInitializeProxy()
    {
        $this->initializeProxy(MockState1::class, true);
        $reflectionClassProxyObject = new \ReflectionClass($this->proxy);
        $proxyNotInitialized = $reflectionClassProxyObject->newInstanceWithoutConstructor();
        try {
            $proxyCloned = clone $proxyNotInitialized;
        } catch (\Exception $e) {
            $this->fail('Error, __clone must manage when the proxy was not initialized via the constructor');
        }
    }
}
