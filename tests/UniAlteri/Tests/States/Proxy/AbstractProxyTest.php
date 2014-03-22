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

namespace UniAlteri\Tests\States\Proxy;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;
use \UniAlteri\States\Proxy\Exception;
use \UniAlteri\States\States;
use \UniAlteri\Tests\Support;

abstract class AbstractProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Support\VirtualState
     */
    protected $_state1;

    /**
     * @var Support\VirtualState
     */
    protected $_state2;

    /**
     * @var Support\VirtualState
     */
    protected $_state3;

    /**
     * @var Proxy\Standard
     */
    protected $_proxy;

    /**
     * Initialize objects for tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_state1 = new Support\VirtualState();
        $this->_state2 = new Support\VirtualState();
        $this->_state3 = new Support\VirtualState();
        $this->_buildProxy();
    }

    /**
     * Build a proxy object, into $this->_proxy to test it
     * @return Proxy\ProxyInterface
     */
    abstract protected function _buildProxy();

    /**
     * Initialize proxy for test, register all states and enable one it
     * @param string $stateToEnable to enable automatically into proxy
     * @param bool $allowingMethodCalling : if state must
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
        $virtualContainer = new Support\VirtualDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    /**
     * Proxy must throw an exception if the registering state name is not a valid string
     */
    public function testRegisterStateInvalidName()
    {
        try{
            $this->_proxy->registerState(array(), $this->_state1);
        }
        catch(Exception\IllegalArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    /**
     * Proxy must throw an exception if the registering state name
     */
    public function testRegisterStateBadName()
    {
        try{
            $this->_proxy->registerState('99', $this->_state1);
        }
        catch(Exception\IllegalName $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalName exception when the stateName does not respect the regex [a-zA-Z][a-zA-Z0-9_\\]+');
    }

    /**
     * Proxy must throws an exception if the state is not an object
     */
    public function testRegisterInvalidState()
    {
        try{
            $this->_proxy->registerState('state1', array());
        }
        catch(\Exception $e){
            return;
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalState exception when the state is not an object');
    }

    /**
     * Proxy must throws an exception if the state does not implement State\StateInterface
     */
    public function testRegisterNonImplementedState()
    {
        try{
            $this->_proxy->registerState('state1', new \DateTime());
        }
        catch(\Exception $e){
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

    public function testUnRegisterStateInvalidName()
    {
        try{
            $this->_proxy->unregisterState(array());
        }
        catch(Exception\IllegalArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    public function testUnRegisterStateNonExistentState()
    {
        try{
            $this->_proxy->unregisterState('NonExistentState');
        }
        catch(Exception\StateNotFound $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    public function testUnRegisterState()
    {
        $this->_initializeProxy();
        $this->_proxy->unregisterState('state2');
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listAvailableStates());
    }

    public function testUnRegisterEnableState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state3');
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listActivesStates());
        $this->_proxy->unregisterState('state3');
        $this->assertEquals(array('state1', 'state2'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listActivesStates());
    }

    public function testSwitchStateInvalidName()
    {
        try{
            $this->_proxy->switchState(array());
        }
        catch(Exception\IllegalArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    public function testSwitchStateNonExistentName()
    {
        try{
            $this->_proxy->switchState('NonExistentState');
        }
        catch(Exception\StateNotFound $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    public function testSwitchState()
    {
        $this->_initializeProxy();
        $this->_proxy->switchState('state3');
        $this->assertEquals(array('state3'), $this->_proxy->listActivesStates());
    }

    public function testSwitchAlreadyLoadedState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->switchState('state2');
        $this->assertEquals(array('state2'), $this->_proxy->listActivesStates());
    }

    public function testEnableStateInvalidName()
    {
        try {
            $this->_proxy->enableState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch(\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    public function testEnableStateNonExistentName()
    {
        try {
            $this->_proxy->enableState('NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch(\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    public function testEnableState()
    {
        $this->_initializeProxy();
        $this->_proxy->disableState('state1');
        $this->_proxy->enableState('state2');
        $this->assertEquals(array('state2'), $this->_proxy->listActivesStates());
    }

    public function testEnableMultipleState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->assertEquals(array('state1', 'state2'), $this->_proxy->listActivesStates());
    }

    public function testDisableStateInvalidName()
    {
        try {
            $this->_proxy->disableState(array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the stateName is not a string');
    }

    public function testDisableStateNonExistentName()
    {
        try {
            $this->_proxy->disableState( 'NonExistentState');
        } catch (Exception\StateNotFound $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the stateName is not register');
    }

    public function testDisableState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->disableState('state1');
        $this->assertEquals(array('state2'), $this->_proxy->listActivesStates());
    }

    public function testDisableAllStates()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_proxy->disableAllStates();
        $this->assertEquals(array(), $this->_proxy->listActivesStates());
    }

    public function testListAvailableStatesOfEmpty()
    {
        $this->assertEquals(array(), $this->_proxy->listAvailableStates());
    }

    public function testListAvailableStates()
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->_proxy->registerState('state3', $this->_state3);
        $this->assertEquals(array('state1', 'state3'), $this->_proxy->listAvailableStates());
    }

    public function testListActivesStatesOfEmpty()
    {
        $this->_proxy->registerState('state1', $this->_state1);
        $this->_proxy->registerState('state3', $this->_state3);
        $this->assertEquals(array(), $this->_proxy->listActivesStates());
    }

    public function testListActivesStates()
    {
        $this->_initializeProxy();
        $this->assertEquals(array('state1'), $this->_proxy->listActivesStates());
    }

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

    public function testGetStatic()
    {
        $state = new Support\VirtualState(function () {
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

    public function testCallInvalidName()
    {
        try{
            $this->_proxy->__call(array(), array());
        }
        catch(Exception\IllegalArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\IllegalArgument exception when the method name is not a string');
    }

    public function testCallWithoutState()
    {
        try{
            $this->_proxy->test();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when no state are available');
    }

    public function testCallMultipleImplementation()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_state1->allowMethod();
        $this->_state2->allowMethod();

        try{
            $this->_proxy->test();
        }
        catch(Exception\AvailableSeveralMethodImplementations $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\AvailableSeveralMethodImplementations when there are multiples implementations of a method in several enabled states');
    }

    public function testCall()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->myCustomMethod('foo', 'bar');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('myCustomMethod', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo', 'bar'), $this->_state1->getCalledArguments());
    }

    public function testCallMethodOfDisabledState()
    {
        $this->_initializeProxy();
        $this->_proxy->enableState('state2');
        $this->_state1->allowMethod();
        $this->_state2->allowMethod();

        try{
            $this->_proxy->testOfState3();
        }
        catch(Exception\UnavailableState $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\UnavailableState when the required state is not enable');
    }

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

    public function testGetMethodDescriptionInvalidName()
    {
        try{
            $this->_proxy->getMethodDescription(array());
        }
        catch(Exception\InvalidArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\InvalidArgument exception when the method namee is not a string');
    }

    public function testGetMethodDescriptionNonExistentName()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->getMethodDescription('NonExistantMethod');
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the method was not found');
    }

    public function testGetMethodDescriptionInvalidStateName()
    {
        $this->_initializeProxy('state1', true);
        try{
            $this->_proxy->getMethodDescription('method1', array());
        }
        catch(Exception\InvalidArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\InvalidArgument exception when the stateName is not a string');
    }

    public function testGetMethodDescriptionInvalidState()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->getMethodDescription('NonExistentMethod', 'NonExistentState');
        }
        catch(Exception\StateNotFound $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\StateNotFound exception when the required state does not exist');
    }

    public function testGetMethodDescription()
    {
        $this->_initializeProxy('state1', true);
        $this->assertInstanceOf('\ReflectionMethod', $this->_proxy->getMethodDescription('test'));
    }

    public function testInvokeNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $proxy = $this->_proxy;
            $proxy();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testInvoke()
    {
        $this->_initializeProxy('state1', true);
        $proxy = $this->_proxy;
        $proxy('foo', 'bar');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__invoke', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo', 'bar'), $this->_state1->getCalledArguments());
    }

    public function testGetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->property;
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testGet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->property;

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__get', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    public function testIssetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $a = isset($this->_proxy->property);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testIsset()
    {
        $this->_initializeProxy('state1', true);
        $a = isset($this->_proxy->property);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__isset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    public function testSetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->property = 'foo';
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testSet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->property = 'foo';

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__set', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property', 'foo'), $this->_state1->getCalledArguments());
    }

    public function testUnsetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            unset($this->_proxy->property);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testUnset()
    {
        $this->_initializeProxy('state1', true);
        unset($this->_proxy->property);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__unset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('property'), $this->_state1->getCalledArguments());
    }

    public function testToStringNonImplemented()
    {
        $this->_initializeProxy();
        $s='error';
        try{
            $s = (string) $this->_proxy;
        }
        catch(\Exception $e){
            $this->fail('Error, the proxy must not throw exception from __toString, forbidden by PHP engine');
        }

        $this->assertEquals('', $s);
    }

    public function testToString()
    {
        $this->_initializeProxy('state1', true);
        $s = (string) $this->_proxy;

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('__toString', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testCountNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->count();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testCount()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->count();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('count', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testOffsetExistNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $a = isset($this->_proxy[2]);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testOffsetExist()
    {
        $this->_initializeProxy('state1', true);
        $a = isset($this->_proxy[2]);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetExists', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    public function testOffsetGetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $value = $this->_proxy[2];
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testOffsetGet()
    {
        $this->_initializeProxy('state1', true);
        $value = $this->_proxy[2];

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetGet', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    public function testOffsetSetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy[2] = 'foo';
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testOffsetSet()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy[2] = 'foo';

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetSet', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2, 'foo'), $this->_state1->getCalledArguments());
    }

    public function testOffsetUnsetNonImplemented()
    {
        $this->_initializeProxy();
        try{
            unset($this->_proxy[2]);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testOffsetUnset()
    {
        $this->_initializeProxy('state1', true);
        unset($this->_proxy[2]);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('offsetUnset', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    public function testCurrentNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->current();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testCurrent()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->current();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('current', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testKeyNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->key();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testKey()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->key();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('key', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testNextNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->next();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testNext()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->next();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('next', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testRewindNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->rewind();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testRewind()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->rewind();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('rewind', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testSeekNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->seek(1);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testSeek()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->seek(2);

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('seek', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(2), $this->_state1->getCalledArguments());
    }

    public function testValidNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->valid();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testValid()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->valid();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('valid', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testGetIteratorNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->getIterator();
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testGetIterator()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->getIterator();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('getIterator', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    public function testSerializeNonImplemented()
    {
        $this->_initializeProxy();
        try{
            serialize($this->_proxy);
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testSerialize()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->serialize();

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('serialize', $this->_state1->getMethodNameCalled());
        $this->assertSame(array(), $this->_state1->getCalledArguments());
    }

    /**
     * Proxy must throw an exception when method is not implemented 
     */
    public function testUnSerializeNonImplemented()
    {
        $this->_initializeProxy();
        try{
            $this->_proxy->unserialize('');
        }
        catch(Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the proxy must throw an Exception\MethodNotImplemented exception when the stateName is not a string');
    }

    public function testUnSerialize()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->unserialize('foo');

        $this->assertTrue($this->_state1->methodWasCalled());
        $this->assertSame('unserialize', $this->_state1->getMethodNameCalled());
        $this->assertSame(array('foo'), $this->_state1->getCalledArguments());
    }

    public function testCloning()
    {
        $this->_initializeProxy('state1', true);
        $this->_proxy->setDIContainer(new Support\VirtualDIContainer());
        $obj = new \stdClass();
        $obj->foo = 'bar';
        $this->_proxy->getDIContainer()->registerInstance('obj', $obj);
        $clonedProxy = clone $this->_proxy;
        //States must be independently
        $this->assertEquals(array('state1', 'state2', 'state3'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listActivesStates());
        $this->assertEquals(array('state1', 'state2', 'state3'), $clonedProxy->listAvailableStates());
        $this->assertEquals(array('state1'), $clonedProxy->listActivesStates());

        //List must perform independently
        $clonedProxy->switchState('state2');
        $clonedProxy->unregisterState('state3');
        $this->assertEquals(array('state1', 'state2', 'state3'), $this->_proxy->listAvailableStates());
        $this->assertEquals(array('state1'), $this->_proxy->listActivesStates());
        $this->assertEquals(array('state1', 'state2'), $clonedProxy->listAvailableStates());
        $this->assertEquals(array('state2'), $clonedProxy->listActivesStates());

        //container must be cloned
        $diContainer = $this->_proxy->getDIContainer();
        $clonedDiContainer = $clonedProxy->getDIContainer();

        $this->assertEquals(get_class($diContainer), get_class($clonedDiContainer));
        $this->assertNotSame($diContainer, $clonedDiContainer);
        $this->assertEquals('bar', $clonedDiContainer->get('obj')->foo);

        //unique ids must differe
        $this->assertNotEquals($this->_proxy->getObjectUniqueId(), $clonedProxy->getObjectUniqueId());
    }
}