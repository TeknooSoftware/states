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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\States\States;

use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\Exception\InvalidArgument;
use Teknoo\States\State\Exception\MethodNotImplemented;
use Teknoo\States\State\StateInterface;
use Teknoo\Tests\Support;

/**
 * Class AbstractStatesTest
 * Set of tests to test the excepted behaviors of all implementations of \Teknoo\States\State\StateInterface *.

 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractStatesTest extends TestCase
{
    public static function setUpBeforeClass(): void
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

    public function testWhenExecuteAnNonExistentMethodExceptionMustBeThrew()
    {
        $args =[
            $this->createMock(ProxyInterface::class),
            'badMethod',
            [1,2],
            StateInterface::VISIBILITY_PRIVATE,
            'My\Stated\ClassName',
            function () {
                self::fail();
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPublicClassObject(false, 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );

        self::assertFalse($called, "Error, if a method does not exist the callback must be never called");
    }

    public function testWhenExecuteAnStaticMethodAnExceptionMustBeNotThrew()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'staticMethod3',
            [],
            StateInterface::VISIBILITY_PRIVATE,
            'My\Stated\ClassName',
            function () {
                self::fail();
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPublicClassObject(false, 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );

        self::assertFalse($called, "Error, if a method does not exist the callback must be never called");
    }

    public function testAnExceptionMustBeThrewWhenTheMethodNameToExecuteIsNotAString()
    {
        $this->expectException(\TypeError::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            [],
            [1,2],
            StateInterface::VISIBILITY_PRIVATE,
            'My\Stated\ClassName',
            function () {
                self::fail();
            }
        ];

        $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->executeClosure(
                ...$args
            );
    }

    public function testAnExceptionMustBeThrewWhenTheScopeToExecuteIsNotAString()
    {
        $this->expectException(InvalidArgument::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            'badScope',
            'My\Stated\ClassName',
            function () {
                self::fail();
            }
        ];

        $this->getPublicClassObject(false, 'My\Stated\ClassName')
            ->executeClosure(
                ...$args
            );
    }

    public function testExecutePrivateMethodInAPrivateScope()
    {
        $args =[
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PRIVATE ,
            'My\Stated\ClassName' ,
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called , 'Error, the private method standardMethod10 has not been called in a private scope');
    }

    public function testExecuteAProtectedMethodInAPrivateScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PRIVATE ,
            'My\Stated\ClassName' ,
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called , 'Error, the protected method standardMethod6 has not been called in a private scope');
    }

    public function testExecuteAPublicMethodInAPrivateScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PRIVATE,
            'My\Stated\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a private scope');
    }

    public function testNonExecutionOfAPrivateMethodInAProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PROTECTED ,
            'Its\Inherited\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the private method standardMethod10 has been called in a protected scope');
    }

    public function testExecuteAProtectedMethodInAProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PROTECTED ,
            'Its\Inherited\ClassName' ,
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called , 'Error, the protected method standardMethod6 has not been called in a protected scope');
    }

    public function testExecuteAPublicMethodInAProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PROTECTED,
            'Its\Inherited\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a protected scope');
    }

    public function testNonExecutionOfAPrivateMethodInAPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PUBLIC ,
            'Its\Another\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the private method standardMethod10 has been called in a public scope');
    }

    public function testNonExecutionOfProtectedMethodInAPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PUBLIC ,
            'Its\Another\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(false , 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the protected method standardMethod6 has been called in a public scope');
    }

    public function testExecutePublicMethodInAPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PUBLIC,
            'Its\Another\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, 'My\Stated\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a public scope');
    }

    public function testNonExecutionOfParentPrivateMethodInPrivateScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PRIVATE ,
            'My\Stated\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the parent private method standardMethod10 has been called in a private scope');
    }

    public function testExecuteParentProtectedMethodInPrivateScope()
    {
        $args =[
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PRIVATE ,
            'My\Stated\ClassName' ,
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called , 'Error, the parent protected method standardMethod6 has not been called in a private scope');
    }

    public function testExecuteParentPublicMethodInPrivateScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PRIVATE,
            'My\Stated\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a private scope');
    }

    public function testNonExecutionOfParentPrivateMethodInProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PROTECTED ,
            'Its\Inherited\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the parent private method standardMethod10 has been called in a protected scope');
    }

    public function testExecuteParentProtectedMethodInProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PROTECTED ,
            'Its\Inherited\ClassName' ,
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called , 'Error, the parent protected method standardMethod6 has not been called in a protected scope');
    }

    public function testExecuteParentPublicMethodInProtectedScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PROTECTED,
            'Its\Inherited\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a protected scope');
    }

    public function testNonExecutionOfParentPrivateMethodInPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            StateInterface::VISIBILITY_PUBLIC ,
            'Its\Another\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getPrivateClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the parent private method standardMethod10 has been called in a public scope');
    }

    public function testNonExecutionOfParentProtectedMethodInPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            StateInterface::VISIBILITY_PUBLIC ,
            'Its\Another\ClassName' ,
            function () use (&$called) {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class ,
            $this->getProtectedClassObject(true , 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called , 'Error, the parent protected method standardMethod6 has been called in a public scope');
    }

    public function testExecuteParentPublicMethodInPublicScope()
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            StateInterface::VISIBILITY_PUBLIC,
            'Its\Another\ClassName',
            function ($result) use (&$called) {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, 'My\Parent\ClassName')
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a public scope');
    }

    public function testExceptionWhenExecutingAMethodWithABadBuilderNotReturningAClosure()
    {
        $this->expectException(MethodNotImplemented::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            'methodBuilderNoReturnClosure',
            [],
            StateInterface::VISIBILITY_PUBLIC,
            'My\Stated\ClassName',
            function () use (&$called) {}
        ];

        $statePublicMock = $this->getPublicClassObject(false, 'My\Stated\ClassName');
        $statePublicMock->executeClosure(
            ...$args
        );
    }
}
