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
 */

namespace UniAlteri\Tests\States\States;

use UniAlteri\States\Proxy;
use UniAlteri\States\State;
use UniAlteri\Tests\Support;

/**
 * Class AbstractStatesTest
 * Set of tests to test the excepted behaviors of all implementations of \UniAlteri\States\State\StateInterface *.
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
     * Build a basic object to provide only public methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyPublic
     */
    abstract protected function getPublicClassObject(bool $privateMode, string $statedClassName);

    /**
     * Build a basic object to provide only protected methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyProtected
     */
    abstract protected function getProtectedClassObject(bool $privateMode, string $statedClassName);

    /**
     * Build a basic object to provide only private methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     *
     * @return Support\MockOnlyPrivate
     */
    abstract protected function getPrivateClassObject(bool $privateMode, string $statedClassName);

    /**
     * Build a virtual proxy for test.
     *
     * @return Proxy\ProxyInterface
     */
    abstract protected function getMockProxy();

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
            $this->getPublicClassObject(false,  'My\Stated\Class')->listMethods()->getArrayCopy()
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
            $this->getProtectedClassObject(false,  'My\Stated\Class')->listMethods()->getArrayCopy()
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
            $this->getPrivateClassObject(false,  'My\Stated\Class')->listMethods()->getArrayCopy()
        );
    }

    /**
     * Test if exception when we get a description of a non-existent method.
     */
    public function testGetBadMethodDescription()
    {
        try {
            $this->getPublicClassObject(false,  'My\Stated\Class')->getMethodDescription('badMethod');
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
            $this->getPublicClassObject(false,  'My\Stated\Class')->getMethodDescription('getMethodDescription');
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of internal method of the trait');
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
        $this->assertSame('Final Method 9.', $this->formatDescription($this->getPrivateClassObject(false,  'My\Stated\Class')->getMethodDescription('finalMethod9')));
        $this->assertSame('Standard Method 10.', $this->formatDescription($this->getPrivateClassObject(false,  'My\Stated\Class')->getMethodDescription('standardMethod10')));

        $this->assertSame('Standard Method 6.           @param $a      @param $b           @return mixed', $this->formatDescription($this->getProtectedClassObject(false,  'My\Stated\Class')->getMethodDescription('standardMethod6')));
        $this->assertSame('Final Method 7.', $this->formatDescription($this->getProtectedClassObject(false,  'My\Stated\Class')->getMethodDescription('finalMethod7')));

        $this->assertSame('Standard Method 1.', $this->formatDescription($this->getPublicClassObject(false,  'My\Stated\Class')->getMethodDescription('standardMethod1')));
        $this->assertSame('Final Method 2.', $this->formatDescription($this->getPublicClassObject(false,  'My\Stated\Class')->getMethodDescription('finalMethod2')));
    }

    public function testTestMethodExceptionWithInvalidScope()
    {
        try {
            $this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod1', 'badScope');
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
        $private = $this->getPrivateClassObject(false,  'My\Stated\Class');
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE));

        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PRIVATE));

        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertFalse($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PRIVATE));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PRIVATE));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected).
     */
    public function testTestMethodProtectedScope()
    {
        $private = $this->getPrivateClassObject(false,  'My\Stated\Class');
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED));

        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PROTECTED));

        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertFalse($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PROTECTED));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PROTECTED));
    }

    /**
     * Test if the method exist into the state into the defined scope (public).
     */
    public function testTestMethodPublicScope()
    {
        $private = $this->getPrivateClassObject(false,  'My\Stated\Class');
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC));

        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PUBLIC));

        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertFalse($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PUBLIC));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PUBLIC));
    }

    /**
     * Test if the method exist into the state into the default scope (public).
     */
    public function testTestMethodDefaultAsPublicScope()
    {
        $private = $this->getPrivateClassObject(false,  'My\Stated\Class');
        $this->assertFalse($private->testMethod('finalMethod9'));
        $this->assertFalse($private->testMethod('finalMethod9'));
        $this->assertFalse($private->testMethod('standardMethod10'));
        $this->assertFalse($private->testMethod('finalMethod11'));
        $this->assertFalse($private->testMethod('staticMethod12'));
        $this->assertFalse($private->testMethod('staticMethod12'));

        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('staticMethod5'));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod6'));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('finalMethod7'));
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->testMethod('standardMethod8'));

        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod1'));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('finalMethod2'));
        $this->assertFalse($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('staticMethod3'));
        $this->assertTrue($this->getPublicClassObject(false,  'My\Stated\Class')->testMethod('standardMethod4'));
    }

    /**
     * Test if the method exist into the state into the defined scope (private) when the private mode enable and caller
     * can be another class (its forbidden), a child class (its forbidden) and the same class (it's granted).
     */
    public function testTestMethodPrivateScopeWithPrivateMode()
    {
        $private = $this->getPrivateClassObject(true,  'My\Stated\Class');
        $private->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));

        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));

        $protected = $this->getProtectedClassObject(true,  'My\Stated\Class');
        $protected->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));

        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));

        $public = $this->getPublicClassObject(true,  'My\Stated\Class');
        $public->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\Class'));

        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PRIVATE, 'Its\Inherited\AnotherClass'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodProtectedScopeWithPrivateMode()
    {
        $private = $this->getPrivateClassObject(true,  'My\Stated\Class');
        $private->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));

        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));

        $protected = $this->getProtectedClassObject(true,  'My\Stated\Class');
        $protected->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertTrue($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));

        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));

        $public = $this->getPublicClassObject(true,  'My\Stated\Class');
        $public->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\Class'));

        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\AnotherClass'));
    }

    /**
     * Test if the method exist into the state into the defined scope (public).when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodPublicScopeWithPrivateMode()
    {
        $private = $this->getPrivateClassObject(true,  'My\Stated\Class');
        $private->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));

        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod9', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('standardMethod10', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('finalMethod11', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($private->testMethod('staticMethod12', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));

        $protected = $this->getProtectedClassObject(true,  'My\Stated\Class');
        $protected->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));

        $this->assertFalse($protected->testMethod('staticMethod5', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($protected->testMethod('standardMethod6', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($protected->testMethod('finalMethod7', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($protected->testMethod('standardMethod8', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));

        $public = $this->getPublicClassObject(true,  'My\Stated\Class');
        $public->setStatedClassName('Its\Inherited\Class')->setPrivateMode(true);
        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\Class'));

        $this->assertTrue($public->testMethod('standardMethod1', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('finalMethod2', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertFalse($public->testMethod('staticMethod3', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
        $this->assertTrue($public->testMethod('standardMethod4', States\StateInterface::VISIBILITY_PUBLIC, 'Its\Inherited\AnotherClass'));
    }

    /**
     * Test exception through by state if the closure method does not exist.
     */
    public function testGetBadClosure()
    {
        try {
            $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure('badMethod');
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
            $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure('staticMethod3');
        } catch (States\Exception\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if the method is static');
    }

    /**
     * Test exception through by state if the scope is invalid.
     */
    public function testGetClosureWithInvalidScope()
    {
        try {
            $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure('standardMethod1', 'badScope');
        } catch (States\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if the scope is invalid');
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureWithPrivateScope()
    {
        $closure = $this->getPrivateClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod10',
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod1',
            States\StateInterface::VISIBILITY_PRIVATE
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureWithProtectedScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(false,  'My\Stated\Class')->getClosure(
                'standardMethod10',
                States\StateInterface::VISIBILITY_PROTECTED
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod1',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureWithPublicScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(false,  'My\Stated\Class')->getClosure(
                'standardMethod10',
                States\StateInterface::VISIBILITY_PUBLIC
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
                'standardMethod6',
                States\StateInterface::VISIBILITY_PUBLIC
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod1',
            States\StateInterface::VISIBILITY_PUBLIC
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureWithPrivateScopeWithPrivateModeSameClass()
    {
        $closure = $this->getPrivateClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod10',
                States\StateInterface::VISIBILITY_PRIVATE,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getProtectedClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod6',
                States\StateInterface::VISIBILITY_PRIVATE,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PRIVATE,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureWithProtectedScopeWithPrivateModeSameClass()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod10',
                    States\StateInterface::VISIBILITY_PROTECTED,
                    'It\A\Stated\Class'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod6',
                States\StateInterface::VISIBILITY_PROTECTED,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PROTECTED,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureWithPublicScopeWithPrivateModeSameClass()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod10',
                    States\StateInterface::VISIBILITY_PUBLIC,
                    'It\A\Stated\Class'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod6',
                    States\StateInterface::VISIBILITY_PUBLIC,
                    'It\A\Stated\Class'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PUBLIC,
                'It\A\Stated\Class'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureWithPrivateScopeWithPrivateModeAnotherClass()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod10',
                    States\StateInterface::VISIBILITY_PRIVATE,
                    'It\A\Stated\AnotherClass'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod6',
                States\StateInterface::VISIBILITY_PRIVATE,
                'It\A\Stated\AnotherClass'
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PRIVATE,
                'It\A\Stated\AnotherClass'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureWithProtectedScopeWithPrivateModeAnotherClass()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod10',
                    States\StateInterface::VISIBILITY_PROTECTED,
                    'It\A\Stated\AnotherClass'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod6',
                States\StateInterface::VISIBILITY_PROTECTED,
                'It\A\Stated\AnotherClass'
        );

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PROTECTED,
                'It\A\Stated\AnotherClass'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureWithPublicScopeWithPrivateModeAnotherClass()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod10',
                    States\StateInterface::VISIBILITY_PUBLIC,
                    'It\A\Stated\AnotherClass'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(true,  'My\Stated\Class')
                ->setPrivateMode(true)
                ->setStatedClassName('It\A\Stated\Class')
                ->getClosure(
                    'standardMethod6',
                    States\StateInterface::VISIBILITY_PUBLIC,
                    'It\A\Stated\AnotherClass'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(true,  'My\Stated\Class')
            ->setPrivateMode(true)
            ->setStatedClassName('It\A\Stated\Class')
            ->getClosure(
                'standardMethod1',
                States\StateInterface::VISIBILITY_PUBLIC,
                'It\A\Stated\AnotherClass'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the default scope (public).
     */
    public function testGetClosureWithPublicAsDefaultScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(false,  'My\Stated\Class')->getClosure(
                'standardMethod10'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
                'standardMethod6'
            );
        } catch (States\Exception\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod1'
        );

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test calling closure from states.
     */
    public function testCallingAfterGetClosure()
    {
        $closure = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertInstanceOf('\Closure', $closure);
        $this->assertEquals(4, $closure(1, 3));
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleSameClosures()
    {
        $projected = $this->getProtectedClassObject(false,  'My\Stated\Class');
        $closure1 = $projected->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure2 = $projected->getClosure(
            'finalMethod7',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure3 = $projected->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertSame($closure1, $closure3);
        $this->assertNotSame($closure1, $closure2);
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleClosuresMultipleState()
    {
        $closure1 = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure2 = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'finalMethod7',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $closure3 = $this->getProtectedClassObject(false,  'My\Stated\Class')->getClosure(
            'standardMethod6',
            States\StateInterface::VISIBILITY_PROTECTED
        );

        $this->assertNotSame($closure1, $closure3);
        $this->assertNotSame($closure1, $closure2);
    }

    /**
     * To check if the mode is by default disable to keep the original behavior.
     */
    public function testPrivateModeIsDisableByDefault()
    {
        $this->assertFalse($this->getPublicClassObject(false,  'My\Stated\Class')->isPrivateMode());
        $this->assertFalse($this->getProtectedClassObject(false,  'My\Stated\Class')->isPrivateMode());
        $this->assertFalse($this->getPrivateClassObject(false,  'My\Stated\Class')->isPrivateMode());
    }

    /**
     * To check behavior of method setPrivateMode() and isPrivateMode().
     */
    public function testPrivateModeEnable()
    {
        $statePublicMock = $this->getPublicClassObject(false,  'My\Stated\Class');
        $stateProtectedMock = $this->getProtectedClassObject(false,  'My\Stated\Class');
        $statePrivateMock = $this->getPrivateClassObject(false,  'My\Stated\Class');

        $statePublicMock->setPrivateMode(true);
        $this->assertTrue($statePublicMock->isPrivateMode());

        $stateProtectedMock->setPrivateMode(true);
        $this->assertTrue($stateProtectedMock->isPrivateMode());

        $statePrivateMock->setPrivateMode(true);
        $this->assertTrue($statePrivateMock->isPrivateMode());
    }

    /**
     * To check behavior of method setPrivateMode() and isPrivateMode().
     */
    public function testPrivateModeDisable()
    {
        $statePublicMock = $this->getPublicClassObject(false,  'My\Stated\Class');
        $stateProtectedMock = $this->getProtectedClassObject(false,  'My\Stated\Class');
        $statePrivateMock = $this->getPrivateClassObject(false,  'My\Stated\Class');

        $statePublicMock->setPrivateMode(false);
        $this->assertFalse($statePublicMock->isPrivateMode());

        $stateProtectedMock->setPrivateMode(false);
        $this->assertFalse($stateProtectedMock->isPrivateMode());

        $statePrivateMock->setPrivateMode(false);
        $this->assertFalse($statePrivateMock->isPrivateMode());
    }

    /**
     * To check behavior of methods getStatedClassName() and setStatedClassName().
     */
    public function testSetAndGetStatedClassName()
    {
        $statePublicMock = $this->getPublicClassObject(false,  'My\Stated\Class');
        $stateProtectedMock = $this->getProtectedClassObject(false,  'My\Stated\Class');
        $statePrivateMock = $this->getPrivateClassObject(false,  'My\Stated\Class');

        $statePublicMock->setStatedClassName('Its\A\Stated\ClassNamePublic');
        $this->assertEquals('Its\A\Stated\ClassNamePublic', $statePublicMock->getStatedClassName());

        $stateProtectedMock->setStatedClassName('Its\A\Stated\ClassNameProtected');
        $this->assertEquals('Its\A\Stated\ClassNameProtected', $stateProtectedMock->getStatedClassName());

        $statePrivateMock->setStatedClassName('Its\A\Stated\ClassNamePrivate');
        $this->assertEquals('Its\A\Stated\ClassNamePrivate', $statePrivateMock->getStatedClassName());
    }
}
