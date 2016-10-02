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
namespace Teknoo\Tests\States\States;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\Exception as StateException;
use Teknoo\Tests\Support;

/**
 * Class AbstractStatesTest
 * Set of tests to test the excepted behaviors of all implementations of \Teknoo\States\State\StateInterface *.
 
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractStatesTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once dirname(dirname(__DIR__)).'/Support/InheritanceFakeClasses.php';
    }

    /**
     * Build a basic object to provide only public methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     * @param array  $aliases
     *
     * @return Support\MockOnlyPublic
     */
    abstract protected function getPublicClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

    /**
     * Build a basic object to provide only protected methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     * @param array  $aliases
     *
     * @return Support\MockOnlyProtected
     */
    abstract protected function getProtectedClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

    /**
     * Build a basic object to provide only private methods.
     *
     * @param bool   $privateMode
     * @param string $statedClassName
     * @param array  $aliases
     *
     * @return Support\MockOnlyPrivate
     */
    abstract protected function getPrivateClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

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
                'methodBuilderNoReturnClosure'
            ),
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->listMethods()
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
            $this->getProtectedClassObject(false, 'My\Stated\ClassName')->listMethods()
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
            $this->getPrivateClassObject(false, 'My\Stated\ClassName')->listMethods()
        );
    }

    /**
     * Test if exception when the name is not a valid string.
     *
     * @expectedException \TypeError
     */
    public function testGetBadNameMethodDescription()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription(array());
    }

    /**
     * Test if exception when we get a description of a non-existent method.
     */
    public function testGetBadMethodDescription()
    {
        try {
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('badMethod');
        } catch (StateException\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if we require a description of non-existent method');
    }

    /**
     * Test if exception when we get a description of an ignored method, the behavior must like non-existent method.
     */
    public function testGetIgnoredMethodDescriptionUsedByTrait()
    {
        try {
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('getMethodDescription');
        } catch (StateException\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) { }

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
        $this->assertSame('Final Method 9.', $this->formatDescription($this->getPrivateClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod9')));
        $this->assertSame('Standard Method 10.', $this->formatDescription($this->getPrivateClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod10')));

        $this->assertSame('Standard Method 6.           @param $a      @param $b           @return mixed', $this->formatDescription($this->getProtectedClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod6')));
        $this->assertSame('Final Method 7.', $this->formatDescription($this->getProtectedClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod7')));

        $this->assertSame('Standard Method 1.', $this->formatDescription($this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod1')));
        $this->assertSame('Final Method 2.', $this->formatDescription($this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod2')));
    }

    /**
     * @expectedException \TypeError
     */
    public function testTestMethodExceptionWithInvalidName()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->testMethod(array(), StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
    }

    public function testTestMethodExceptionWithInvalidScope()
    {
        try {
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->testMethod('standardMethod1', 'badScope', 'My\Stated\ClassName');
        } catch (StateException\InvalidArgument $e) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if we require a description with an invalid scope name');
    }

    /**
     * Test if the method exist into the state into the defined scope (private) when the private mode enable and caller
     * can be another class (its forbidden), a child class (its forbidden) and the same class (it's granted).
     */
    public function testTestMethodFromPrivateScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        $this->assertTrue($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodFromProtectedScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodFromPublicScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (private) when the private mode enable and caller
     * can be another class (its forbidden), a child class (its forbidden) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromPrivateScope()
    {
        $private = $this->getPrivateClassObject(true, 'My\Parent\ClassName');
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $protected = $this->getProtectedClassObject(true, 'My\Parent\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $public = $this->getPublicClassObject(true, 'My\Parent\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromProtectedScope()
    {
        $private = $this->getPrivateClassObject(true, 'My\Parent\ClassName');
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $protected = $this->getProtectedClassObject(true, 'My\Parent\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $public = $this->getPublicClassObject(true, 'My\Parent\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromPublicScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Parent\ClassName');
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Parent\ClassName');
        $this->assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Parent\ClassName');
        $this->assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        $this->assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
    }

    /**
     * Test exception through by state if the closure method does not exist.
     */
    public function testGetBadClosure()
    {
        try {
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('badMethod', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
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
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\MethodNotImplemented exception if the method is static');
    }

    /**
     * Test exception through by state if the name is invalid.
     *
     * @expectedException \TypeError
     */
    public function testGetClosureWithInvalidName()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure(array(), StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
    }

    /**
     * Test exception through by state if the scope is invalid.
     */
    public function testGetClosureWithInvalidScope()
    {
        try {
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('standardMethod1', 'badScope', 'My\Stated\ClassName');
        } catch (StateException\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the state must throws an Exception\InvalidArgument exception if the scope is invalid');
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureFromPrivateScope()
    {
        $closure = $this->getPrivateClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getProtectedClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureFromProtectedScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(false, 'My\Stated\ClassName')
                ->getClosure('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureFromPublicScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(false, 'My\Stated\ClassName')
                ->getClosure('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(false, 'My\Stated\ClassName')
                ->getClosure('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureFromParentFromPrivateScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true, 'My\Parent\ClassName')
                ->getClosure('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {}

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (protected, so privates methods are not available).
     */
    public function testGetClosureFromParentFromProtectedScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true, 'My\Parent\ClassName')
                ->getClosure('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        $this->assertInstanceOf('\Closure', $closure);

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test if the closure can be get into the state into the defined scope (public).
     */
    public function testGetClosureFromParentFromPublicScope()
    {
        $fail = false;
        try {
            $this->getPrivateClassObject(true, 'My\Parent\ClassName')
                ->getClosure('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(true, 'My\Parent\ClassName')
                ->getClosure('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');

        $this->assertInstanceOf('\Closure', $closure);
    }

    /**
     * Test calling closure from states.
     */
    public function testCallingAfterGetClosure()
    {
        $closure = $this->getProtectedClassObject(false, 'My\Stated\ClassName')->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $this->assertInstanceOf('\Closure', $closure);
        $this->assertEquals(4, $closure(1, 3));
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleSameClosures()
    {
        $projected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $closure1 = $projected->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $closure2 = $projected->getClosure(
            'finalMethod7',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $closure3 = $projected->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $closure4 = $projected->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $this->assertSame($closure1, $closure3);
        $this->assertNotSame($closure1, $closure2);
        $this->assertSame($closure1, $closure4);
    }

    /**
     * Test multiple call to getClosure for the same method.
     */
    public function testGetMultipleClosuresMultipleState()
    {
        $closure1 = $this->getProtectedClassObject(false, 'My\Stated\ClassName')->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $closure2 = $this->getProtectedClassObject(false, 'My\Stated\ClassName')->getClosure(
            'finalMethod7',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $closure3 = $this->getProtectedClassObject(false, 'My\Stated\ClassName')->getClosure(
            'standardMethod6',
            StateInterface::VISIBILITY_PROTECTED,
            'My\Stated\ClassName'
        );

        $this->assertNotSame($closure1, $closure3);
        $this->assertNotSame($closure1, $closure2);
    }

    /**
     * To check if the mode is by default disable to keep the original behavior.
     */
    public function testPrivateModeIsDisableByDefault()
    {
        $this->assertFalse($this->getPublicClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
        $this->assertFalse($this->getProtectedClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
        $this->assertFalse($this->getPrivateClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
    }

    /**
     * To check behavior of method setPrivateMode() and isPrivateMode().
     */
    public function testPrivateModeEnable()
    {
        $statePublicMock = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $stateProtectedMock = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $statePrivateMock = $this->getPrivateClassObject(false, 'My\Stated\ClassName');

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
        $statePublicMock = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $stateProtectedMock = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $statePrivateMock = $this->getPrivateClassObject(false, 'My\Stated\ClassName');

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
        $statePublicMock = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $stateProtectedMock = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        $statePrivateMock = $this->getPrivateClassObject(false, 'My\Stated\ClassName');

        $statePublicMock->setStatedClassName('Its\A\Stated\ClassNamePublic');
        $this->assertEquals('Its\A\Stated\ClassNamePublic', $statePublicMock->getStatedClassName());

        $stateProtectedMock->setStatedClassName('Its\A\Stated\ClassNameProtected');
        $this->assertEquals('Its\A\Stated\ClassNameProtected', $stateProtectedMock->getStatedClassName());

        $statePrivateMock->setStatedClassName('Its\A\Stated\ClassNamePrivate');
        $this->assertEquals('Its\A\Stated\ClassNamePrivate', $statePrivateMock->getStatedClassName());
    }

    /**
     * @expectedException \Teknoo\States\State\Exception\MethodNotImplemented
     */
    public function testExceptionOnBadBuilder()
    {
        $statePublicMock = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $statePublicMock->getClosure('methodBuilderNoReturnClosure', StateInterface::VISIBILITY_PUBLIC, 'My\Stated\ClassName');
    }
}
