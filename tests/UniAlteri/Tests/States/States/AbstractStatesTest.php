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

namespace UniAlteri\Tests\States\States;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;
use \UniAlteri\States\States;
use \UniAlteri\Tests\Support;

abstract class AbstractStatesTest extends \PHPUnit_Framework_TestCase{

    /**
     * Build an basic object to provide only public methods
     * @return Support\OnlyPublic
     */
    abstract protected function _getPublicClassObject();

    /**
     * Build an basic object to provide only protected methods
     * @return Support\OnlyProtected
     */
    abstract protected function _getProtectedClassObject();

    /**
     * Build an basic object to provide only private methods
     * @return Support\OnlyPrivate
     */
    abstract protected function _getPrivateClassObject();

    /**
     * Build a virtual proxy for test
     * @return Proxy\ProxyInterface
     */
    abstract protected function _getVirtualProxy();

    /**
     * Test if the state can return all its public method, without static
     */
    public function testListMethodsPublic(){
        $this->assertEquals(
            array(
                'standardMethod1',
                'finalMethod2',
                'standardMethod4'
            ),
            $this->_getPublicClassObject()->listMethods()
        );
    }

    /**
     * Test if the state can return all its protected method, without static
     */
    public function testListMethodsProtected(){
        $this->assertEquals(
            array(
                '_standardMethod6',
                '_finalMethod7',
                '_standardMethod8'
            ),
            $this->_getProtectedClassObject()->listMethods()
        );
    }

    /**
     * Test if the state can return all its private method, without static
     */
    public function testListMethodsPrivate(){
        $this->assertEquals(
            array(
                '_finalMethod9',
                '_standardMethod10',
                '_finalMethod11'
            ),
            $this->_getPrivateClassObject()->listMethods()
        );
    }

    /**
     * Test if exception when the name is not a valid string
     */
    public function testGetBadNameMethodDescription(){
        try{
            $this->_getPublicClassObject()->getMethodDescription(array());
        }
        catch(States\Exception\InvalidArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid string');
    }

    /**
     * Test if exception when we get a description of a non-existent method
     */
    public function testGetBadMethodDescription(){
        try{
            $this->_getPublicClassObject()->getMethodDescription('badMethod');
        }
        catch(States\Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test if exception when we get a description of a static method
     */
    public function testGetStaticMethodDescription(){
        try{
            $this->_getPublicClassObject()->getMethodDescription('staticMethod3');
        }
        catch(States\Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of static method');
    }

    /**
     * Test get method description
     */
    public function testGetMethodDescription(){
        $this->assertSame('Final Method 9', $this->_getPrivateClassObject()->getMethodDescription('_finalMethod9'));
        $this->assertSame('Standard Method 10', $this->_getPrivateClassObject()->getMethodDescription('_standardMethod10'));

        $this->assertSame('Standard Method 6', $this->_getProtectedClassObject()->getMethodDescription('_standardMethod6'));
        $this->assertSame('Final Method 7', $this->_getProtectedClassObject()->getMethodDescription('_finalMethod7'));

        $this->assertSame('Standard Method 1', $this->_getPublicClassObject()->getMethodDescription('standardMethod1'));
        $this->assertSame('Final Method 2', $this->_getPublicClassObject()->getMethodDescription('finalMethod2'));
    }

    public function testTestMethodExceptionWithInvalidName(){
        try{
            $this->_getPublicClassObject()->testMethod(array());
        }
        catch(States\Exception\InvalidArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid string');
    }

    /**
     * Test the test method this method must throws never an exception, only return false or true
     */
    public function testTestMethod(){
        $this->assertTrue($this->_getPrivateClassObject()->testMethod('_finalMethod9'));
        $this->assertTrue($this->_getPrivateClassObject()->testMethod('_standardMethod10'));
        $this->assertTrue($this->_getPrivateClassObject()->testMethod('_finalMethod11'));
        $this->assertFalse($this->_getPrivateClassObject()->testMethod('_staticMethod12'));

        $this->assertFalse($this->_getProtectedClassObject()->testMethod('_staticMethod5'));
        $this->assertTrue($this->_getProtectedClassObject()->testMethod('_standardMethod6'));
        $this->assertTrue($this->_getProtectedClassObject()->testMethod('_finalMethod7'));
        $this->assertTrue($this->_getProtectedClassObject()->testMethod('_standardMethod8'));

        $this->assertTrue($this->_getPublicClassObject()->testMethod('standardMethod1'));
        $this->assertTrue($this->_getPublicClassObject()->testMethod('finalMethod2'));
        $this->assertFalse($this->_getPublicClassObject()->testMethod('staticMethod3'));
        $this->assertTrue($this->_getPublicClassObject()->testMethod('standardMethod4'));
    }

    /**
     * Test exception through by state if the name is invalid
     */
    public function testGetClosureWithInvalidName(){
        try{
            $this->_getPublicClassObject()->getClosure(array(), $this->_getVirtualProxy());
        }
        catch(States\Exception\InvalidArgument $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description of non-existent method');
    }

    /**
     * Test exception through by state if the closure method does not exist
     */
    public function testGetBadClosure(){
        try{
            $this->_getPublicClassObject()->getClosure('badMethod', $this->_getVirtualProxy());
        }
        catch(States\Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test exception through by state if the closure method is static
     */
    public function testGetStaticClosure(){
        try{
            $this->_getPublicClassObject()->getClosure('standardMethod1', $this->_getVirtualProxy());
        }
        catch(States\Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test exception through by state if the closure method is static
     */
    public function testGetClosureWithInvalidProxy(){
        try{
            $this->_getPublicClassObject()->getClosure('standardMethod1', new \DateTime());
        }
        catch(States\Exception\MethodNotImplemented $e){
            return;
        }
        catch(\Exception $e){
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test Closure
     */
    public function testGetClosure(){
        $closure = $this->_getProtectedClassObject()->getClosure('_standardMethod6', $this->_getVirtualProxy());
        $this->assertSame('DI\InjectionClosure', $closure);
        $this->assertSame('\Closure', $closure->getClosure());
        $this->assertEquals(3, call_user_func_array($closure->getClosure(), array(1, 2)));
    }

    /**
     * Test multiple call go getClosure for the same method
     */
    public function testGetMultipleSameClosures(){
        $closure1 = $this->_getProtectedClassObject()->getClosure('_standardMethod6', $this->_getVirtualProxy());
        $closure2 = $this->_getProtectedClassObject()->getClosure('_finalMethod7', $this->_getVirtualProxy());
        $closure3 = $this->_getProtectedClassObject()->getClosure('_standardMethod8', $this->_getVirtualProxy());

        $this->assertSame($closure1, $closure3);
        $this->assertSame($closure1->getClosure(), $closure3->getClosure());
        $this->assertNotSame($closure1, $closure2);
        $this->assertNotSame($closure1->getClosure(), $closure2->getClosure());
    }
}
