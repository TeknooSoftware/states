<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\States;

use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\Exception\InvalidArgument;
use Teknoo\States\State\Exception\MethodNotImplemented;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\Visibility;
use Teknoo\Tests\Support;

/**
 * Class AbstractStatesTests
 * Set of tests to test the excepted behaviors of all implementations of \Teknoo\States\State\StateInterface *.

 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractStatesTests extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__DIR__, 2).'/Support/InheritanceFakeClasses.php';
    }

    /**
     * Build a basic object to provide only public methods.
     *
     *
     * @return Support\MockOnlyPublic
     */
    abstract protected function getPublicClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

    /**
     * Build a basic object to provide only protected methods.
     *
     *
     * @return Support\MockOnlyProtected
     */
    abstract protected function getProtectedClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

    /**
     * Build a basic object to provide only private methods.
     *
     *
     * @return Support\MockOnlyPrivate
     */
    abstract protected function getPrivateClassObject(bool $privateMode, string $statedClassName, array $aliases = []);

    /**
     * Clean description text to simplify tests.
     *
     * @param \ReflectionMethod $text
     */
    protected function formatDescription($text): ?string
    {
        $s = trim(str_replace(['*', '/'], '', (string) $text->getDocComment()));

        return preg_replace('~[[:cntrl:]]~', '', $s);
    }

    public function testWhenExecuteAnNonExistentMethodExceptionMustBeThrew(): void
    {
        $args =[
            $this->createMock(ProxyInterface::class),
            'badMethod',
            [1,2],
            Visibility::Private,
            \My\Stated\ClassName::class,
            function (): never {
                self::fail();
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );

        self::assertFalse($called, "Error, if a method does not exist the callback must be never called");
    }

    public function testWhenExecuteAnStaticMethodAnExceptionMustBeNotThrew(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'staticMethod3',
            [],
            Visibility::Private,
            \My\Stated\ClassName::class,
            function (): never {
                self::fail();
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );

        self::assertFalse($called, "Error, if a method does not exist the callback must be never called");
    }

    public function testAnExceptionMustBeThrewWhenTheMethodNameToExecuteIsNotAString(): void
    {
        $this->expectException(\TypeError::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            [],
            [1,2],
            Visibility::Private,
            \My\Stated\ClassName::class,
            function (): never {
                self::fail();
            }
        ];

        $this->getPublicClassObject(false, \My\Stated\ClassName::class)
            ->executeClosure(
                ...$args
            );
    }

    public function testAnExceptionMustBeThrewWhenTheScopeToExecuteIsNotAString(): void
    {
        $this->expectException(\TypeError::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            'badScope',
            \My\Stated\ClassName::class,
            function (): never {
                self::fail();
            }
        ];

        $this->getPublicClassObject(false, \My\Stated\ClassName::class)
            ->executeClosure(
                ...$args
            );
    }

    public function testExecutePrivateMethodInAPrivateScope(): void
    {
        $args =[
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Private ,
            \My\Stated\ClassName::class ,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the private method standardMethod10 has not been called in a private scope');
    }

    public function testExecuteAProtectedMethodInAPrivateScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Private ,
            \My\Stated\ClassName::class ,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the protected method standardMethod6 has not been called in a private scope');
    }

    public function testExecuteAPublicMethodInAPrivateScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Private,
            \My\Stated\ClassName::class,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a private scope');
    }

    public function testNonExecutionOfAPrivateMethodInAProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Protected ,
            \Its\Inherited\ClassName::class ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the private method standardMethod10 has been called in a protected scope');
    }

    public function testExecuteAProtectedMethodInAProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Protected ,
            \Its\Inherited\ClassName::class ,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the protected method standardMethod6 has not been called in a protected scope');
    }

    public function testExecuteAPublicMethodInAProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Protected,
            \Its\Inherited\ClassName::class,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a protected scope');
    }

    public function testNonExecutionOfAPrivateMethodInAPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Public ,
            'Its\Another\ClassName' ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the private method standardMethod10 has been called in a public scope');
    }

    public function testNonExecutionOfProtectedMethodInAPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Public ,
            'Its\Another\ClassName' ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the protected method standardMethod6 has been called in a public scope');
    }

    public function testExecutePublicMethodInAPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Public,
            'Its\Another\ClassName',
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(false, \My\Stated\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the public method standardMethod1 has not been called in a public scope');
    }

    public function testNonExecutionOfParentPrivateMethodInPrivateScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Private ,
            \My\Stated\ClassName::class ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the parent private method standardMethod10 has been called in a private scope');
    }

    public function testExecuteParentProtectedMethodInPrivateScope(): void
    {
        $args =[
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Private ,
            \My\Stated\ClassName::class ,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent protected method standardMethod6 has not been called in a private scope');
    }

    public function testExecuteParentPublicMethodInPrivateScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Private,
            \My\Stated\ClassName::class,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a private scope');
    }

    public function testNonExecutionOfParentPrivateMethodInProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Protected ,
            \Its\Inherited\ClassName::class ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the parent private method standardMethod10 has been called in a protected scope');
    }

    public function testExecuteParentProtectedMethodInProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Protected ,
            \Its\Inherited\ClassName::class ,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent protected method standardMethod6 has not been called in a protected scope');
    }

    public function testExecuteParentPublicMethodInProtectedScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Protected,
            \Its\Inherited\ClassName::class,
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a protected scope');
    }

    public function testNonExecutionOfParentPrivateMethodInPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod10' ,
            [1,2] ,
            Visibility::Public ,
            'Its\Another\ClassName' ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPrivateClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the parent private method standardMethod10 has been called in a public scope');
    }

    public function testNonExecutionOfParentProtectedMethodInPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class) ,
            'standardMethod6' ,
            [1,2] ,
            Visibility::Public ,
            'Its\Another\ClassName' ,
            function () use (&$called): void {
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getProtectedClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertFalse($called, 'Error, the parent protected method standardMethod6 has been called in a public scope');
    }

    public function testExecuteParentPublicMethodInPublicScope(): void
    {
        $args = [
            $this->createMock(ProxyInterface::class),
            'standardMethod1',
            [1,2],
            Visibility::Public,
            'Its\Another\ClassName',
            function ($result) use (&$called): void {
                self::assertEquals(3, $result);
                $called = true;
            }
        ];

        $called = false;
        self::assertInstanceOf(
            StateInterface::class,
            $this->getPublicClassObject(true, \My\Parent\ClassName::class)
                ->executeClosure(
                    ...$args
                )
        );
        self::assertTrue($called, 'Error, the parent public method standardMethod1 has not been called in a public scope');
    }

    public function testExceptionWhenExecutingAMethodWithABadBuilderNotReturningAClosure(): void
    {
        $this->expectException(MethodNotImplemented::class);
        $args = [
            $this->createMock(ProxyInterface::class),
            'methodBuilderNoReturnClosure',
            [],
            Visibility::Public,
            \My\Stated\ClassName::class,
            function () use (&$called): void {
            }
        ];

        $statePublicMock = $this->getPublicClassObject(false, \My\Stated\ClassName::class);
        $statePublicMock->executeClosure(
            ...$args
        );
    }
}
