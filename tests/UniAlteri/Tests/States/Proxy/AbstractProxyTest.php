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
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\States\Proxy;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;
use \UniAlteri\States\Proxy\Exception;
use \UniAlteri\States\States;
use \UniAlteri\Tests\Support;

/**
 * Class AbstractProxyTest
 * Abstract tests case to test the excepted behavior of each proxy implementing the interface
 * Proxy\ProxyInterface
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
abstract class AbstractProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock state 1, used in these tests
     * @var Support\MockState
     */
    protected $_state1;

    /**
     * Mock state 2, used in these tests
     * @var Support\MockState
     */
    protected $_state2;

    /**
     * Mock state 3, used in these tests
     * @var Support\MockState
     */
    protected $_state3;

    /**
     * Proxy to test and validate
     * @var Proxy\ProxyInterface
     */
    protected $_proxy;

    /**
     * Initialize objects for tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_state1 = new Support\MockState();
        $this->_state2 = new Support\MockState();
        $this->_state3 = new Support\MockState();
        $this->_buildProxy();
    }

    /**
     * Build a proxy object, into $this->_proxy to test it
     * @return Proxy\ProxyInterface
     */
    abstract protected function _buildProxy();

    /**
     * Initialize proxy for test, register all states and enable one it
     * @param string $stateToEnable         to enable automatically into proxy
     * @param bool   $allowingMethodCalling : if state must
     */
    protected function _initializeProxy($stateToEnable = 'state1', $allowingMethodCalling=false)
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->_proxy->registerState('state2', $this->_state2);
        $this->_proxy->registerState('state3', $this->_state3);
        $this->_proxy->enableState($stateToEnable);
        if (true === $allowingMethodCalling) {
            $this->{'_'.$stateToEnable}->allowMethod();
        } else {
            $this->{'_'.$stateToEnable}->disallowMethod();
        }
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = $this->_buildProxy();
        try {
            $object->setDIContainer(new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the DI Container is not valid');
    }

    /**
     * Test behavior for methods Set And GetDiContainer
     */
    public function testSetAndGetDiContainer()
    {
        $object = $this->_buildProxy();
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\MockDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string
     */
    public function testRegisterStateInvalidName()
    {
        try {
            $this->_proxy->registerState(array(), $this->_state1);
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string
     */
    public function testRegisterStateBadName()
    {
        try {
            $this->_proxy->registerState('99', $this->_state1);
        } catch (Exception\IllegalName $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\IllegalName exception when the stateName does not respect the regex [a-zA-Z][a-zA-Z0-9_\\]+');
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string
     */
    public function testRegisterInvalidState()
    {
        try {
            $this->_proxy->registerState('state1', array());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalState exception when the state is not an object');
    }

    /**
     * Proxy must throw an exception if the registering state object does not implement the interface State\StateInterface
     */
    public function testRegisterNonImplementedState()
    {
        try {
            $this->_proxy->registerState('state1', new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalState exception when the state does not implement State\StateInterface');
    }

    /**
     * Check behavior of the proxy when we add a new state
     */
    public function testRegisterState()
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->assertEquals(array('state1'), $this->_proxy->listAvailableStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string
     */
    public function testUnRegisterStateInvalidName()
    {
        try {
            $this->_proxy->unregisterState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the state to remove is not registered
     */
    public function testUnRegisterStateNonExistentState()
    {
        try {
            $this->_proxy->unregisterState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Test proxy behavior to unregister a state
     */
    public function testUnRegisterState()
    {
        $this->_initializeProxy();
        $this->_proxy->unregisterState('state2');
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listAvailableStates());
    }

    /**
     * Test proxy behavior to unregister an active state
     */
    public function testUnRegisterEnableState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state3');
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listEnabledStates());
        $this->_proxy->unregisterState('state3');
        $this->assertEquals(array('state1', 'state2'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string in switch state method
     */
    public function testSwitchStateInvalidName()
    {
        try {
            $this->_proxy->switchState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the state does not exist in switch state method
     */
    public function testSwitchStateNonExistentName()
    {
        try {
            $this->_proxy->switchState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Test proxy behavior when we switch of states
     */
    public function testSwitchState()
    {
        $this->_initializeProxy();
        $this->_proxy->switchState('state3');
        $this->assertEquals(array('state3'), $this->_proxy->listEnabledStates());
    }

    /**
     * Test proxy behavior when we switch to already enable state
     */
    public function testSwitchAlreadyLoadedState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->switchState('state2');
        $this->assertEquals(array('state2'), $this->_proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want enable a state
     */
    public function testEnableStateInvalidName()
    {
        try {
            $this->_proxy->enableState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state
     */
    public function testEnableStateNonExistentName()
    {
        try {
            $this->_proxy->enableState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Check proxy behavior when we enable a state
     */
    public function testEnableState()
    {
        $this->_initializeProxy();
        $this->_proxy->disableState('state1');
        $this->_proxy->enableState('state2');
        $this->assertEquals(array('state2'), $this->_proxy->listEnabledStates());
    }

    /**
     * Check proxy behavior when we enable multiple states
     */
    public function testEnableMultipleState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->assertEquals(array('state1', 'state2'), $this->_proxy->listEnabledStates());
    }

    /**
     * Proxy must throw an exception if the state name is not a valid string when we want disable a state
     */
    public function testDisableStateInvalidName()
    {
        try {
            $this->_proxy->disableState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the state is not available when we want enable a state
     */
    public function testDisableStateNonExistentName()
    {
        try {
            $this->_proxy->disableState( 'NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    /**
     * Check proxy behavior when we disable a state
     */
    public function testDisableState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->disableState('state1');
        $this->assertEquals(array('state2'), $this->_proxy->listEnabledStates());
    }

    /**
     * Check proxy behavior when we disable multiple states
     */
    public function testDisableAllStates()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->disableAllStates();
        $this->assertEquals(array(), $this->_proxy->listEnabledStates());
    }

    /**
     * Check the proxy's method listAvailableStates behavior when there are no registered state
     */
    public function testListAvailableStatesOfEmpty()
    {
        $this->assertEquals(array(), $this->_proxy->listAvailableStates());
    }

    /**
     * Check the proxy's method listAvailableStates behavior
     */
    public function testListAvailableStates()
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->_proxy->registerState('state3', $this->_state3);
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listAvailableStates());
    }

    /**
     * Check the proxy's method listEnabledStates behavior when there are no enable state
     */
    public function testListEnabledStatesOfEmpty()
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->_proxy->registerState('state3', $this->_state3);
        $this->assertEquals(array(), $this->_proxy->listEnabledStates());
    }

    /**
     * Check the proxy's method listEnabledStates behavior
     */
    public function testListEnabledStates()
    {
        $this->_initializeProxy();
        $this->assertEquals(array('state1'), $this->_proxy->listEnabledStates());
    }

    /**
     * Test proxy behavior when we call the method getStatic() outside of a stated class's methods
     */
    public function testGetStaticWithoutCalling()
    {
        $this->_initializeProxy('state1', true);
        try {
            $this->_proxy->getStatic();
        } catch (Exception\UnavailableClosure $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Only current closure can call the method getStatic() to register static var, else, proxy must throws Exception\UnavailableClosure');
    }

    /**
     * Test the behavior of the proxy method getStatic in a normal call
     */
    public function testGetStatic()
    {
        $state = new Support\MockState(function () {
            $this->getStatic()->saveProperty('name', 'value');
        });

        $this->_proxy->registerState(
            'static',
            $state
        );

        $state->allowMethod();
        $this->_proxy->enableState('static');

        $closure = $state->getClosure('__invoke', $this->_proxy);
        call_user_func(array($this->_proxy, '__invoke'));
        $this->assertEquals('value', $closure->getProperty('name'));
    }

    /**
     * Test the behavior of the proxy method getStatic when there are some exception in a stated class's method :
     * The proxy must restore the previous state
     */
    public function testGetStaticRestoredAfterException()
    {
        $state = new Support\MockState(function () {
            throw new \Exception('failure');
            $this->getStatic()->saveProperty('name', 'value');
        });

        $this->_proxy->registerState(
            'static',
            $state
        );

        $state->allowMethod();
        $this->_proxy->enableState('static');

        $closure = $state->getClosure('__invoke', $this->_proxy);
        $fail = false;
        try {
            call_user_func(array($this->_proxy, '__invoke'));
        } catch ( \Exception $e) {
            $fail = true;
        }

        $this->assertTrue($fail, 'Error, proxy must throw the exception of the called closure');
        $this->assertEquals(null, $closure->getProperty('name'));

        try {
            $this->_proxy->getStatic();
        } catch (Exception\UnavailableClosure $e) {
            return;
        } catch ( \Exception $e) {}
        $this->fail('Error, the proxy was not restored previous injection closure state during the exception');
    }

    /**
     * Test the behavior of the proxy method getStatic when there are some exception in a stated class's method :
     * The proxy must restore the previous state
     */
    public function testGetStaticRestoredAfterExceptionWithSpecificState()
    {
        $state = new Support\MockState(function () {
            throw new \Exception('failure');
            $this->getStatic()->saveProperty('name', 'value');
        });

        $this->_proxy->registerState(
            'static',
            $state
        );

        $state->allowMethod();
        $this->_proxy->enableState('static');

        $closure = $state->getClosure('__invoke', $this->_proxy);
        $fail = false;
        try {
            call_user_func(array($this->_proxy, '__invokeOfStatic'));
        } catch ( \Exception $e) {
            $fail = true;
        }

        $this->assertTrue($fail, 'Error, proxy must throw the exception of the called closure');
        $this->assertEquals(null, $closure->getProperty('name'));

        try {
            $this->_proxy->getStatic();
        } catch (Exception\UnavailableClosure $e) {
            return;
        } catch ( \Exception $e) {}
        $this->fail('Error, the proxy was not restored previous injection closure state during the exception');
    }

    /**
     * Test proxy behavior when the called method name is not a string
     */
    public function testCallInvalidName()
    {
        try {
            $this->_proxy->__call(array(), array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the method name is not a string');
    }

    /**
     * Test proxy behavior when the required method is not implemented in anything active state
     */
    public function testCallNonImplementedWithoutState()
    {
        try {
            $this->_proxy->test();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when no state are available');
    }

    /**
     * Test proxy behavior when the required method is not implemented in the required state
     */
    public function testCallNonImplementedWithState()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->testOfState1();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when no state are available');
    }

    /**
     * Test proxy behavior when the required method is implemented in several active state
     */
    public function testCallMultipleImplementation()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_state1->allowMethod();
        $this->_state2->allowMethod();

        try {
            $this->_proxy->test();
        } catch (Exception\AvailableSeveralMethodImplementations $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\AvailableSeveralMethodImplementations when there are multiples implementations of a method in several enabled states');
    }

    /**
     * Test proxy behavior in a normal calling
     */
    public function testCall()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->myCustomMethod('foo', 'bar');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('myCustomMethod', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo', 'bar'), $this->_state1->getCalledArguments());
    }

    /**
     * Test proxy behavior when the required state in the call is not enabled
     */
    public function testCallMethodOfDisabledState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_state1->allowMethod();
        $this->_state2->allowMethod();

        try {
            $this->_proxy->testOfState3();
        } catch (Exception\UnavailableState $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\UnavailableState when the required state is not enabled');
    }

    /**
     * Test proxy behavior in a normal calling when the required state is defined in the call
     */
    public function testCallMethodOfState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->enableState('state3');
        $this->_state1->allowMethod();
        $this->_state2->allowMethod();
        $this->_state3->allowMethod();
        $this->_proxy->testOfState2('bar', 'foo');

        $this->assertFalse($this->_state1->methodWasCalled());
        $this->assertTrue($this->_state2->methodWasCalled());
        $this->assertFalse($this->_state3->methodWasCalled());
        $this->assertEquals(array('bar', 'foo'), $this->_state2->getCalledArguments());
        $this->assertEquals('test', $this->_state2->getMethodNameCalled());
    }

    /**
     * Test the proxy behavior when the method name is not a valid string when we want its description
     */
    public function testGetMethodDescriptionInvalidName()
    {
        try {
            $this->_proxy->getMethodDescription(array());
        } catch (Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\InvalidArgument exception when the method name is not a string');
    }

    /**
     * Test the proxy behavior when hen we want a description of a non existent method
     */
    public function testGetMethodDescriptionNonExistentName()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->getMethodDescription('NonExistentMethod');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the method was not found');
    }

    /**
     * Test the proxy behavior when hen we want a description of a non existent method in the required state
     */
    public function testGetMethodDescriptionNonExistentNameByState()
    {
        $this->_initializeProxy('state1', true);
        $this->_state1->simulateFailureInGetMethodDescription();
        try {
            $this->_proxy->getMethodDescription('NonExistentMethod');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the method was not found');
    }

    /**
     * Test the proxy behavior when hen we want a description of a method and the required state name is not a string
     */
    public function testGetMethodDescriptionInvalidStateName()
    {
        $this->_initializeProxy('state1', true);
        try {
            $this->_proxy->getMethodDescription('method1', array());
        } catch (Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\InvalidArgument exception when the stateName is not a string');
    }

    /**
     * Test the proxy behavior when hen we want a description of a method and the required state does not exist
     */
    public function testGetMethodDescriptionInvalidState()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->getMethodDescription('NonExistentMethod', 'NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the required state does not exist');
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // Following tests check the method getMethodDescription in different visibility scope //
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Test getMethodDescription from a function for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromFunction()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a private method
        $fail = false;
        try {
            testGetMethodDescriptionFromFunctionPrivate();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a protected method
        $fail = false;
        try {
            testGetMethodDescriptionFromFunctionProtected();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a public method
        $this->assertInstanceOf('\ReflectionMethod', testGetMethodDescriptionFromFunctionPublic());
    }

    /**
     * Test getMethodDescription from another object for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromOtherObject()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new \testGetMethodDescriptionFromOtherObject();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $fail = false;
        try {
            $object = new \testGetMethodDescriptionFromOtherObject();
            $object->protectedMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new \testGetMethodDescriptionFromOtherObject();
        $this->assertInstanceOf('\ReflectionMethod', $object->publicMethod());
    }

    /**
     * Test getMethodDescription from an object of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromChildObject()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new $childClassName();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new $childClassName;
        $this->assertInstanceOf('\ReflectionMethod', $object->protectedMethod());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new $childClassName;
        $this->assertInstanceOf('\ReflectionMethod', $object->publicMethod());
    }

    /**
     * Test getMethodDescription from another object from the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromOtherObjectSameClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of private methods
        $proxy2 = new $childClassName;
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
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromThis()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

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
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromStaticOtherClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of private methods
        $fail = false;
        try {
            \testGetMethodDescriptionFromOtherObject::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of protected methods
        $fail = false;
        try {
            \testGetMethodDescriptionFromOtherObject::protectedMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of public methods
        $this->assertInstanceOf('\ReflectionMethod', \testGetMethodDescriptionFromOtherObject::publicMethodStatic());
    }

    /**
     * Test getMethodDescription from a static method of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromStaticChildClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $childClassName::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

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
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromStaticSameClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsDescription.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildDescription';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testGetMethodDescriptionTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

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
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromClosure()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        $proxy = $this->_proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a private method
        $fail = false;
        try {
            $closure = function () use ($proxy) {
                return $proxy->getMethodDescription('privateTest');
            };
            $closure();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a protected method
        $fail = false;
        try {
            $closure = function () use ($proxy) {
                return $proxy->getMethodDescription('protectedTest');
            };
            $closure();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

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
     * - a public method : return description if the method exists
     */
    public function testGetMethodDescriptionFromClosureBound()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        $proxy = $this->_proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a private method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('privateTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a protected method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('protectedTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a public method
        $closureOriginal = function () use ($proxy) {
            return $proxy->getMethodDescription('publicTest');
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $this->assertInstanceOf('\ReflectionMethod', $closure());
    }

    /////////////////////////////////////////////////////////////////////////////////
    // End tests for the method getMethodDescription in different visibility scope //
    /////////////////////////////////////////////////////////////////////////////////

    /**
     * Test the proxy behavior when we require a description of a method of a required state
     */
    public function testGetMethodDescriptionOfState()
    {
        $this->_initializeProxy('state1', true);
        $this->_state2->allowMethod();
        $this->assertInstanceOf('\ReflectionMethod', $this->_proxy->getMethodDescription('test', 'state2'));
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Following tests check if the proxy respects visibility restriction private/protected/public of called methods//
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check proxy respects visibility restriction on methods from a function for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testCallFromFunction()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a private method
        $fail = false;
        try {
            testCallFromFunctionPrivate();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a protected method
        $fail = false;
        try {
            testCallFromFunctionProtected();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a function to get a description of a public method
        testCallFromFunctionPublic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testCallFromOtherObject()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $fail = false;
        try {
            $object = new \testCallFromOtherObject();
            $object->protectedMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new \testCallFromOtherObject();
        $object->publicMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from an object of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromChildObject()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $object = new $childClassName();
            $object->privateMethod();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $object = new $childClassName;
        $object->protectedMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $object = new $childClassName;
        $object->publicMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from another object from the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromOtherObjectSameClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testCallTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of private methods
        $proxy2 = new $childClassName;
        $proxy2->privateMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('privateTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of protected methods
        $proxy2->protectedMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class object to get a description of public methods
        $proxy2->publicMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from this object (called from one of its methods) for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromThis()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testCallTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of private methods
        $proxy->privateMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('privateTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of protected methods
        $proxy->protectedMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from $this to get a description of public methods
        $proxy->publicMethod();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of another class for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testCallFromStaticOtherClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of private methods
        $fail = false;
        try {
            \testCallFromOtherObject::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of protected methods
        $fail = false;
        try {
            \testCallFromOtherObject::protectedMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external class to get a description of public methods
        \testCallFromOtherObject::publicMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of inherited class for :
     * - a private method : throw exception non implemented
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromStaticChildClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        global $proxy;
        $proxy = $this->_proxy;

        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testCallTrait; }';
        eval($code);

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of private methods
        $fail = false;
        try {
            $childClassName::privateMethodStatic();
        } catch (Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, private methods are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a external object to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a static method of the same class for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromStaticSameClass()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        include_once 'UniAlteri/Tests/Support/TestVisibilityFunctionsCall.php';

        //Create a temp child class to test
        $classNamePartArray = explode('\\', get_class($this->_proxy));
        $childClassName = array_pop($classNamePartArray);
        $childClassName = $childClassName.'ChildClass';
        $code = 'if (class_exists("'.$childClassName.'")) {return;}'.PHP_EOL.'class '.$childClassName.' extends '.get_class($this->_proxy).'{ use testCallTrait; }';
        eval($code);

        /**
         * In this test, use a child proxy and not directly the proxy because we can not add on the fly
         * method into the proxy to run the test
         */
        global $proxy;
        $proxy = new $childClassName();
        $proxy->registerState('state1', $this->_state1);
        $proxy->registerState('state2', $this->_state2);
        $proxy->registerState('state3', $this->_state3);
        $proxy->enableState('state1');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of private methods
        $childClassName::privateMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('privateTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of protected methods
        $childClassName::protectedMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a same class to get a description of public methods
        $childClassName::publicMethodStatic();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure for :
     * - a private method : throw exception non implemented
     * - a protected method : throw exception non implemented
     * - a public method : return description if the method exists
     */
    public function testCallFromClosure()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        $proxy = $this->_proxy;

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
        } catch (\Exception $e) {}
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
        } catch (\Exception $e) {}
        $this->assertTrue($fail, 'It is a public scope, protected method are not available here');

        //Build temp functions to test proxy behavior with different scope visibility
        //from a closure to get a description of a public method
        $closure = function () use ($proxy) {
            return $proxy->publicTest();
        };
        $closure();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Check proxy respects visibility restriction on methods from a closure bound with this current object for :
     * - a private method : return description if the method exists
     * - a protected method : return description if the method exists
     * - a public method : return description if the method exists
     */
    public function testCallFromClosureBound()
    {
        $this->_initializeProxy('state1', true);
        //To access to the proxy in the method
        $proxy = $this->_proxy;

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a private method
        $closureOriginal = function () use ($proxy) {
            return $proxy->privateTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $closure();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('privateTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a protected method
        $closureOriginal = function () use ($proxy) {
            return $proxy->protectedTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $closure();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('protectedTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());

        //Build temp functions to test proxy behavior with different scope visibility
        //from a bound closure to get a description of a public method
        $closureOriginal = function () use ($proxy) {
            return $proxy->publicTest();
        };
        $closure = \Closure::bind($closureOriginal, $this->_proxy);
        $closure();
        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('publicTest', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    ////////////////////////////////////////////////////////////////////////////////////
    // End tests for the method getMethodDescription in different visibility scope    //
    // For all other magics methods : PHP and interface define these methods as public//
    ////////////////////////////////////////////////////////////////////////////////////

    /**
     * Test exception behavior of the proxy when __invoke is not implemented into in actives states
     */
    public function testInvokeNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $proxy = $this->_proxy;
            $proxy();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __invoke is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __invoke. If the call is forwarded to the active state
     */
    public function testInvoke()
    {
        $this->_initializeProxy('state1', true);
        $proxy = $this->_proxy;
        $proxy('foo', 'bar');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__invoke', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo', 'bar'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __get is not implemented into in actives states
     */
    public function testGetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->property;
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __get is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __get. If the call is forwarded to the active state
     */
    public function testGet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->property;

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__get', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __isset is not implemented into in actives states
     */
    public function testIssetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $a = isset($this->_proxy->property);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __isset is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __isset. If the call is forwarded to the active state
     */
    public function testIsset()
    {
        $this->_initializeProxy('state1', true);
        $a = isset($this->_proxy->property);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__isset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __set is not implemented into in actives states
     */
    public function testSetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->property = 'foo';
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __set is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __set. If the call is forwarded to the active state
     */
    public function testSet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->property = 'foo';

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__set', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property', 'foo'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __unset is not implemented into in actives states
     */
    public function testUnsetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            unset($this->_proxy->property);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __unset is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method __unset. If the call is forwarded to the active state
     */
    public function testUnset()
    {
        $this->_initializeProxy('state1', true);
        unset($this->_proxy->property);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__unset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when __toString is not implemented into in actives states
     */
    public function testToStringNonImplemented()
    {
        $this->_initializeProxy();
        $s='error';
        try {
            $s = (string) $this->_proxy;
        } catch (\Exception $e) {
            $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when __toString is not implemented into in actives states');
        }

        $this->assertEquals('', $s);
    }

    /**
     * Test proxy behavior with the magic method __toString. If the call is forwarded to the active state
     */
    public function testToString()
    {
        $this->_initializeProxy('state1', true);
        $s = (string) $this->_proxy;

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__toString', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when count is not implemented into in actives states
     */
    public function testCountNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->count();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when count is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method count. If the call is forwarded to the active state
     */
    public function testCount()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->count();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('count', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetExist is not implemented into in actives states
     */
    public function testOffsetExistNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $a = isset($this->_proxy[2]);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetExist is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetExist. If the call is forwarded to the active state
     */
    public function testOffsetExist()
    {
        $this->_initializeProxy('state1', true);
        $a = isset($this->_proxy[2]);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetExists', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetGet is not implemented into in actives states
     */
    public function testOffsetGetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $value = $this->_proxy[2];
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetGet is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetGet. If the call is forwarded to the active state
     */
    public function testOffsetGet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy[2];

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetGet', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetSet is not implemented into in actives states
     */
    public function testOffsetSetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy[2] = 'foo';
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetSet is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetSet. If the call is forwarded to the active state
     */
    public function testOffsetSet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy[2] = 'foo';

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetSet', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2, 'foo'), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when offsetUnset is not implemented into in actives states
     */
    public function testOffsetUnsetNonImplemented()
    {
        $this->_initializeProxy();
        try {
            unset($this->_proxy[2]);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when offsetUnset is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method offsetUnset. If the call is forwarded to the active state
     */
    public function testOffsetUnset()
    {
        $this->_initializeProxy('state1', true);
        unset($this->_proxy[2]);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetUnset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when current is not implemented into in actives states
     */
    public function testCurrentNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->current();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when current is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method current. If the call is forwarded to the active state
     */
    public function testCurrent()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->current();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('current', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when key is not implemented into in actives states
     */
    public function testKeyNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->key();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when key is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method key. If the call is forwarded to the active state
     */
    public function testKey()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->key();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('key', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when next is not implemented into in actives states
     */
    public function testNextNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->next();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when next is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method next. If the call is forwarded to the active state
     */
    public function testNext()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->next();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('next', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when rewind is not implemented into in actives states
     */
    public function testRewindNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->rewind();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when rewind is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method rewind. If the call is forwarded to the active state
     */
    public function testRewind()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->rewind();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('rewind', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when seek is not implemented into in actives states
     */
    public function testSeekNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->seek(1);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when seek is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method seek. If the call is forwarded to the active state
     */
    public function testSeek()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->seek(2);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('seek', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when valid is not implemented into in actives states
     */
    public function testValidNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->valid();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when valid is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method valid. If the call is forwarded to the active state
     */
    public function testValid()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->valid();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('valid', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when getIterator is not implemented into in actives states
     */
    public function testGetIteratorNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->getIterator();
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when getIterator is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method getIterator. If the call is forwarded to the active state
     */
    public function testGetIterator()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->getIterator();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('getIterator', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when serialize is not implemented into in actives states
     */
    public function testSerializeNonImplemented()
    {
        $this->_initializeProxy();
        try {
            serialize($this->_proxy);
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when serialize is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method serialize. If the call is forwarded to the active state
     */
    public function testSerialize()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->serialize();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('serialize', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Test exception behavior of the proxy when unserialize is not implemented into in actives states
     */
    public function testUnSerializeNonImplemented()
    {
        $this->_initializeProxy();
        try {
            $this->_proxy->unserialize('');
        } catch (Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when unserialize is not implemented into in actives states');
    }

    /**
     * Test proxy behavior with the magic method unserialize. If the call is forwarded to the active state
     */
    public function testUnSerialize()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->unserialize('foo');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('unserialize', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo'), $this->_state1->getCalledArguments());
    }

    /**
     * Test the behavior of the proxy when it is cloned :
     * All states must be cloned
     * DI Container must be cloned
     * Registered states must be cloned
     * Active states must be cloned
     * The cloned proxy must has a new unique id
     */
    public function testCloning()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->setDIContainer(new Support\MockDIContainer());
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $this->_proxy->getDIContainer()->registerInstance('obj', $obj);
        $clonedProxy = clone $this->_proxy;

        //States must be independently
        $this->assertEquals(array('state1', 'state2', 'state3'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listEnabledStates());
        $this->assertEquals(array('state1', 'state2', 'state3'), $clonedProxy->listAvailableStates());
        $this->assertEquals(array('state1'), $clonedProxy->listEnabledStates());

        //List must perform independently
        $clonedProxy->switchState('state2');
        $clonedProxy->unregisterState('state3');
        $this->assertEquals(array('state1', 'state2', 'state3'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listEnabledStates());
        $this->assertEquals(array('state1', 'state2'), $clonedProxy->listAvailableStates());
        $this->assertEquals(array('state2'), $clonedProxy->listEnabledStates());

        //container must be cloned
        $diContainer = $this->_proxy->getDIContainer();
        $clonedDiContainer = $clonedProxy->getDIContainer();

        $this->assertEquals(get_class($diContainer), get_class($clonedDiContainer));
        $this->assertNotSame($diContainer, $clonedDiContainer);
        $this->assertEquals('bar', $clonedDiContainer->get('obj')->foo);

        //unique ids must differ
        $this->assertNotEquals($this->_proxy->getObjectUniqueId(), $clonedProxy->getObjectUniqueId());
    }
}
