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
        self::assertEquals(
            array(
                'standardMethod1',
                'finalMethod2',
                'standardMethod4',
                'methodBuilderNoReturnClosure',
            ),
            $this->getPublicClassObject(false, 'My\Stated\ClassName')->listMethods()
        );
    }

    /**
     * Test if the state can return all its protected method, without static.
     */
    public function testListMethodsProtected()
    {
        self::assertEquals(
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
        self::assertEquals(
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
     *
     * @expectedException \Teknoo\States\State\Exception\MethodNotImplemented
     */
    public function testGetBadMethodDescription()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('badMethod');
    }

    /**
     * Test if exception when we get a description of an ignored method, the behavior must like non-existent method.
     *
     * @expectedException \Teknoo\States\State\Exception\MethodNotImplemented
     */
    public function testGetIgnoredMethodDescriptionUsedByTrait()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('getMethodDescription');
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
        self::assertSame('Final Method 9.', $this->formatDescription($this->getPrivateClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod9')));
        self::assertSame('Standard Method 10.', $this->formatDescription($this->getPrivateClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod10')));

        self::assertSame('Standard Method 6.           @param $a      @param $b           @return mixed', $this->formatDescription($this->getProtectedClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod6')));
        self::assertSame('Final Method 7.', $this->formatDescription($this->getProtectedClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod7')));

        self::assertSame('Standard Method 1.', $this->formatDescription($this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('standardMethod1')));
        self::assertSame('Final Method 2.', $this->formatDescription($this->getPublicClassObject(false, 'My\Stated\ClassName')->getMethodDescription('finalMethod2')));
    }

    /**
     * @expectedException \TypeError
     */
    public function testTestMethodExceptionWithInvalidName()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->testMethod(array(), StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
    }

    /**
     * @expectedException \Teknoo\States\State\Exception\InvalidArgument
     */
    public function testTestMethodExceptionWithInvalidScope()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->testMethod('standardMethod1', 'badScope', 'My\Stated\ClassName');
    }

    /**
     * Test if the method exist into the state into the defined scope (private) when the private mode enable and caller
     * can be another class (its forbidden), a child class (its forbidden) and the same class (it's granted).
     */
    public function testTestMethodFromPrivateScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        self::assertTrue($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodFromProtectedScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodFromPublicScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Stated\ClassName');
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Stated\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (private) when the private mode enable and caller
     * can be another class (its forbidden), a child class (its forbidden) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromPrivateScope()
    {
        $private = $this->getPrivateClassObject(true, 'My\Parent\ClassName');
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $protected = $this->getProtectedClassObject(true, 'My\Parent\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));

        $public = $this->getPublicClassObject(true, 'My\Parent\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromProtectedScope()
    {
        $private = $this->getPrivateClassObject(true, 'My\Parent\ClassName');
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $protected = $this->getProtectedClassObject(true, 'My\Parent\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));

        $public = $this->getPublicClassObject(true, 'My\Parent\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName'));
    }

    /**
     * Test if the method exist into the state into the defined scope (protected). when the private mode enable and caller
     * can be another class (its granted), a child class (its granted) and the same class (it's granted).
     */
    public function testTestMethodOfParentFromPublicScope()
    {
        $private = $this->getPrivateClassObject(false, 'My\Parent\ClassName');
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('finalMethod9', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('standardMethod10', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('finalMethod11', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($private->testMethod('staticMethod12', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $protected = $this->getProtectedClassObject(false, 'My\Parent\ClassName');
        self::assertFalse($protected->testMethod('staticMethod5', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('finalMethod7', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($protected->testMethod('standardMethod8', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));

        $public = $this->getPublicClassObject(false, 'My\Parent\ClassName');
        self::assertTrue($public->testMethod('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertTrue($public->testMethod('finalMethod2', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertFalse($public->testMethod('staticMethod3', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
        self::assertTrue($public->testMethod('standardMethod4', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName'));
    }

    /**
     * Test exception through by state if the closure method does not exist.
     *
     * @expectedException \Teknoo\States\State\Exception\MethodNotImplemented
     */
    public function testGetBadClosure()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('badMethod', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
    }

    /**
     * Test exception through by state if the closure method is static.
     *
     * @expectedException \Teknoo\States\State\Exception\MethodNotImplemented
     */
    public function testGetStaticClosure()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('staticMethod3', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');
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
     *
     * @expectedException \Teknoo\States\State\Exception\InvalidArgument
     */
    public function testGetClosureWithInvalidScope()
    {
        $this->getPublicClassObject(false, 'My\Stated\ClassName')->getClosure('standardMethod1', 'badScope', 'My\Stated\ClassName');
    }

    /**
     * Test if the closure can be get into the state into the defined scope (private).
     */
    public function testGetClosureFromPrivateScope()
    {
        $closure = $this->getPrivateClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod10', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);

        $closure = $this->getProtectedClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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

        self::assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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

        self::assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(false, 'My\Stated\ClassName')
                ->getClosure('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        self::assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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
        } catch (\Exception $e) {
        }

        self::assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PRIVATE, 'My\Stated\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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

        self::assertTrue($fail, 'Error, in Protected scope, private methods are not available');

        $closure = $this->getProtectedClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod6', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PROTECTED, 'Its\Inherited\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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

        self::assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $fail = false;
        try {
            $this->getProtectedClassObject(true, 'My\Parent\ClassName')
                ->getClosure('standardMethod6', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');
        } catch (StateException\MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        self::assertTrue($fail, 'Error, in Public scope, private and protected methods are not available');

        $closure = $this->getPublicClassObject(true, 'My\Parent\ClassName')
            ->getClosure('standardMethod1', StateInterface::VISIBILITY_PUBLIC, 'Its\Another\ClassName');

        self::assertInstanceOf(\Closure::class, $closure);
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

        self::assertInstanceOf(\Closure::class, $closure);
        self::assertEquals(4, $closure(1, 3));
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

        self::assertSame($closure1, $closure3);
        self::assertNotSame($closure1, $closure2);
        self::assertSame($closure1, $closure4);
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

        self::assertNotSame($closure1, $closure3);
        self::assertNotSame($closure1, $closure2);
    }

    /**
     * To check if the mode is by default disable to keep the original behavior.
     */
    public function testPrivateModeIsDisableByDefault()
    {
        self::assertFalse($this->getPublicClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
        self::assertFalse($this->getProtectedClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
        self::assertFalse($this->getPrivateClassObject(false, 'My\Stated\ClassName')->isPrivateMode());
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
        self::assertTrue($statePublicMock->isPrivateMode());

        $stateProtectedMock->setPrivateMode(true);
        self::assertTrue($stateProtectedMock->isPrivateMode());

        $statePrivateMock->setPrivateMode(true);
        self::assertTrue($statePrivateMock->isPrivateMode());
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
        self::assertFalse($statePublicMock->isPrivateMode());

        $stateProtectedMock->setPrivateMode(false);
        self::assertFalse($stateProtectedMock->isPrivateMode());

        $statePrivateMock->setPrivateMode(false);
        self::assertFalse($statePrivateMock->isPrivateMode());
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
        self::assertEquals('Its\A\Stated\ClassNamePublic', $statePublicMock->getStatedClassName());

        $stateProtectedMock->setStatedClassName('Its\A\Stated\ClassNameProtected');
        self::assertEquals('Its\A\Stated\ClassNameProtected', $stateProtectedMock->getStatedClassName());

        $statePrivateMock->setStatedClassName('Its\A\Stated\ClassNamePrivate');
        self::assertEquals('Its\A\Stated\ClassNamePrivate', $statePrivateMock->getStatedClassName());
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
