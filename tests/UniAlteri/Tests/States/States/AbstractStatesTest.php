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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.1.0
 */

namespace UniAlteri\Tests\States\States;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;
use UniAlteri\States\States;
use UniAlteri\Tests\Support;

/**
 * Class AbstractStatesTest
 * Set of tests to test the excepted behaviors of all implementations of \UniAlteri\States\States\StateInterface *.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
abstract class AbstractStatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Build an basic object to provide only public methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyPublic
     */
    abstract protected function getPublicClassObject($initializeContainer = true);

    /**
     * Build an basic object to provide only protected methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyProtected
     */
    abstract protected function getProtectedClassObject($initializeContainer = true);

    /**
     * Build an basic object to provide only private methods.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     *
     * @return Support\MockOnlyPrivate
     */
    abstract protected function getPrivateClassObject($initializeContainer = true);

    /**
     * Build a virtual proxy for test.
     *
     * @return Proxy\ProxyInterface
     */
    abstract protected function getMockProxy();

    /**
     * Test exception when the Container is not valid when we set a bad object as di container.
     */
    public function testSetDiContainerBad()
    {
        $object = $this->getPublicClassObject(false);
        try {
            $object->setDIContainer(new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the DI Container is not valid');
    }

    /**
     * Test behavior for methods Set And GetDiContainer.
     */
    public function testSetAndGetDiContainer()
    {
        $object = $this->getPublicClassObject(false);
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\MockDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    /**
     * Test if the state can return all its public method, without static.
     */
    public function testListMethodsPublic()
    {
        $this->assertEquals(
            array(
                'standardMethod1',
                'finalMethod2',
                'standardMethod4',
            ),
            $this->getPublicClassObject()->listMethods()->getArrayCopy()
        );
    }

    /**
     * Test if the state can return all its protected method, without static.
     */
    public function testListMethodsProtected()
    {
        $this->assertEquals(
            array(
                'standardMethod6',
                'finalMethod7',
                'standardMethod8',
            ),
            $this->getProtectedClassObject()->listMethods()->getArrayCopy()
        );
    }

    /**
     * Test if the state can return all its private method, without static.
     */
    public function testListMethodsPrivate()
    {
        $this->assertEquals(
            array(
                'finalMethod9',
                'standardMethod10',
                'finalMethod11',
            ),
            $this->getPrivateClassObject()->listMethods()->getArrayCopy()
        );
    }

    /**
     * Test if exception when the name is not a valid string.
     */
    public function testGetBadNameMethodDescription()
    {
        try {
            $this->getPublicClassObject()->getMethodDescription(array());
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid string');
    }

    /**
     * Test if exception when we get a description of a non-existent method.
     */
    public function testGetBadMethodDescription()
    {
        try {
            $this->getPublicClassObject()->getMethodDescription('badMethod');
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test if exception when we get a description of an ignored method, the behavior must like non-existent method.
     */
    public function testGetIgnoredMethodDescriptionUsedByTrait()
    {
        try {
            $this->getPublicClassObject()->getMethodDescription('setDIContainer');
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of internal method of the trait');
    }

    /**
     * Test if exception when we get a description of a static method.
     */
    public function testGetStaticMethodDescription()
    {
        try {
            $this->getPublicClassObject()->getMethodDescription('staticMethod3');
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of a static method');
    }

    /**
     * Clean description text to simplify tests.
     *
     * @param \ReflectionMethod $text
     *
     * @return string
     */
    protected function formatDescription($text)
    {
        $s = trim(str_replace(array('*', '/'), '', $text->getDocComment()));

        return preg_replace('~[[:cntrl:]]~', '', $s);
    }

    /**
     * Test get method description.
     */
    public function testGetMethodDescription()
    {
        $this->assertSame('Final Method 9.', $this->formatDescription($this->getPrivateClassObject()->getMethodDescription('finalMethod9')));
        $this->assertSame('Standard Method 10.', $this->formatDescription($this->getPrivateClassObject()->getMethodDescription('standardMethod10')));

        $this->assertSame('Standard Method 6.           @param $a      @param $b           @return mixed', $this->formatDescription($this->getProtectedClassObject()->getMethodDescription('standardMethod6')));
        $this->assertSame('Final Method 7.', $this->formatDescription($this->getProtectedClassObject()->getMethodDescription('finalMethod7')));

        $this->assertSame('Standard Method 1.', $this->formatDescription($this->getPublicClassObject()->getMethodDescription('standardMethod1')));
        $this->assertSame('Final Method 2.', $this->formatDescription($this->getPublicClassObject()->getMethodDescription('finalMethod2')));
    }

    public function testTestMethodExceptionWithInvalidName()
    {
        try {
            $this->getPublicClassObject()->testMethod(array());
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid string');
    }

    public function testTestMethodExceptionWithInvalidScope()
    {
        try {
            $this->getPublicClassObject()->testMethod('standardMethod1', 'badScope');
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid scope name');
    }

    /**
     * Test if the method exist into the state into the defined scope (private).
     */
    public function testTestMethodPrivateScope()
    {
        $private = $this->getPrivateClassObject();
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE));

        $this->assertFalse($this->getProtectedClassObject()->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PRIVATE));

        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getPublicClassObject()->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($this->getPublicClassObject()->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PRIVATE));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected).
     */
    public function testTestMethodProtectedScope()
    {
        $private = $this->getPrivateClassObject();
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED));

        $this->assertFalse($this->getProtectedClassObject()->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject()->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PROTECTED));

        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getPublicClassObject()->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($this->getPublicClassObject()->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PROTECTED));
    }

    /**
     * Test if the method exist into the state into the defined scope (public).
     */
    public function testTestMethodPublicScope()
    {
        $private = $this->getPrivateClassObject();
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC));

        $this->assertFalse($this->getProtectedClassObject()->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PUBLIC));

        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertTrue($this->getPublicClassObject()->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getPublicClassObject()->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PUBLIC));
    }

    /**
     * Test if the method exist into the state into the default scope (public).
     */
    public function testTestMethodDefaultAsPublicScope()
    {
        $private = $this->getPrivateClassObject();
        $this->assertFalse($private->testMethod('finalMethod9'));
        $this->assertFalse($private->testMethod('finalMethod9'));
        $this->assertFalse($private->testMethod('standardMethod10'));
        $this->assertFalse($private->testMethod('finalMethod11'));
        $this->assertFalse($private->testMethod('staticMethod12'));
        $this->assertFalse($private->testMethod('staticMethod12'));

        $this->assertFalse($this->getProtectedClassObject()->testMethod('staticMethod5'));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('standardMethod6'));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('finalMethod7'));
        $this->assertFalse($this->getProtectedClassObject()->testMethod('standardMethod8'));

        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod1'));
        $this->assertTrue($this->getPublicClassObject()->testMethod('finalMethod2'));
        $this->assertFalse($this->getPublicClassObject()->testMethod('staticMethod3'));
        $this->assertTrue($this->getPublicClassObject()->testMethod('standardMethod4'));
    }

    /**
     * Test exception through by state if the name is invalid.
     */
    public function testGetClosureWithInvalidName()
    {
        try {
            $this->getPublicClassObject()->getClosure(array(), $this->getMockProxy());
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if the method name is not a string');
    }

    /**
     * Test exception through by state if the closure method does not exist.
     */
    public function testGetBadClosure()
    {
        try {
            $this->getPublicClassObject()->getClosure('badMethod', $this->getMockProxy());
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if the method does not exist');
    }

    /**
     * Test exception through by state if the closure method is static.
     */
    public function testGetStaticClosure()
    {
        try {
            $this->getPublicClassObject()->getClosure('staticMethod3', $this->getMockProxy());
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if the method is static');
    }

    /**
     *
     */
    public function testGetClosureWithInvalidProxy()
    {
        try {
            $this->getPublicClassObject()->getClosure('standardMethod1', new \DateTime());
        } catch (States\Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if the proxy does not implement the interface Proxy\ProxyInterface');
    }

    /**
     * Test exception through by state if the scope is invalid.
     */
    public function testGetClosureWithInvalidScope()
    {
        try {
            $this->getPublicClassObject()->getClosure('standardMethod1', $this->getMockProxy(), 'badScope');
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if the scope is invalid');
    }

    /**
     * Test exception through by state if the closure method is static.
     */
    public function testGetClosureWithInvalidDiContainer()
    {
        try {
            $object = $this->getPublicClassObject(false);
            $object->getClosure('standardMethod1', $this->getMockProxy());
        } catch (States\Exception\IllegalService $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\IllegalService if no DI Container has been defined before getClosure');
    }

    /**
     * Test exception through by state if the closure method is static.
     */
    public function testGetClosureWithInvalidInjectContainer()
    {
        try {
            $object = $this->getPublicClassObject();
            $object->getDIContainer()->registerService(States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER, null);
            $object->getClosure('standardMethod1', $this->getMockProxy());
        } catch (States\Exception\IllegalService $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\IllegalService if no Injection Container service has been defined before getClosure');
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureWithPrivateScope()
    {
        $closure = $this->getPrivateClassObject()->getClosure(
            'standardMethod10',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);

        $closure = $this->getProtectedClassObject()->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);

        $closure = $this->getPublicClassObject()->getClosure(
            'standardMethod1',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureWithProtectedScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject()->getClosure(
                'standardMethod10',
                $this->getMockProxy(),
                States\StateInterface::VISIBILITY_PROTECTED
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject()->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);

        $closure = $this->getPublicClassObject()->getClosure(
            'standardMethod1',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureWithPublicScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject()->getClosure(
                'standardMethod10',
                $this->getMockProxy(),
                States\StateInterface::VISIBILITY_PUBLIC
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject()->getClosure(
                'standardMethod6',
                $this->getMockProxy(),
                States\StateInterface::VISIBILITY_PUBLIC
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject()->getClosure(
            'standardMethod1',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PUBLIC
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
    }

    /**
     * Test if the closure can be get into the state into the default scope (public).
     */
    public function testGetClosureWithPublicAsDefaultScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject()->getClosure(
                'standardMethod10',
                $this->getMockProxy()
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject()->getClosure(
                'standardMethod6',
                $this->getMockProxy()
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject()->getClosure(
            'standardMethod1',
            $this->getMockProxy()
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
    }

    /**
     * Test calling closure from states.
     */
    public function testCallingAfterGetClosure()
    {
        $closure = $this->getProtectedClassObject()->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
        $this->assertInstanceOf('\Closure', $closure->getClosure());
        $this->assertEquals(3, call_user_func_array($closure->getClosure(), array(1, 2)));
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleSameClosures()
    {
        $projected = $this->getProtectedClassObject();
        $closure1 = $projected->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure2 = $projected->getClosure(
            'finalMethod7',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure3 = $projected->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertSame($closure1, $closure3);
        $this->assertSame($closure1->getClosure(), $closure3->getClosure());
        $this->assertNotSame($closure1, $closure2);
        $this->assertNotSame($closure1->getClosure(), $closure2->getClosure());
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleClosuresMultipleState()
    {
        $closure1 = $this->getProtectedClassObject()->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure2 = $this->getProtectedClassObject()->getClosure(
            'finalMethod7',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure3 = $this->getProtectedClassObject()->getClosure(
            'standardMethod6',
            $this->getMockProxy(),
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertNotSame($closure1, $closure3);
        $this->assertNotSame($closure1->getClosure(), $closure3->getClosure());
        $this->assertNotSame($closure1, $closure2);
        $this->assertNotSame($closure1->getClosure(), $closure2->getClosure());
    }
}
