<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
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

namespace Teknoo\Tests\States\PHPStan;

use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use PHPStan\Cache\Cache;
use PHPStan\Parser\Parser;
use PHPStan\Parser\FunctionCallStatementFinder;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use Teknoo\States\PHPStan\MethodsClassExtension;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Draft;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\PHPStan\MethodsClassExtension
 */
class MethodsClassExtensionTest extends TestCase
{
    private ?ReflectionProvider $reflectionProvider = null;

    private ?Parser $parser = null;

    private ?FunctionCallStatementFinder $functionCallStatementFinder = null;

    private ?Cache $cache = null;

    private function getReflectionProviderMock(): ReflectionProvider
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    private function getParserMock(): Parser
    {
        if (!$this->parser instanceof Parser) {
            $this->parser = $this->createMock(Parser::class);
        }

        return $this->parser;
    }

    private function getFunctionCallStatementFinderMock(): FunctionCallStatementFinder
    {
        if (!$this->functionCallStatementFinder instanceof FunctionCallStatementFinder) {
            $this->functionCallStatementFinder = $this->createMock(FunctionCallStatementFinder::class);
        }

        return $this->functionCallStatementFinder;
    }

    private function getCacheMock(): Cache
    {
        if (!$this->cache instanceof Cache) {
            $this->cache = $this->createMock(Cache::class);
        }

        return $this->cache;
    }

    protected function buildInstance(): MethodsClassReflectionExtension
    {
        return new MethodsClassExtension(
            $this->getParserMock(),
            $this->getFunctionCallStatementFinderMock(),
            $this->getCacheMock(),
            $this->getReflectionProviderMock(),
            $this->createMock(InitializerExprTypeResolver::class),
        );
    }

    public function testHasMethodIsInterface(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(true);

        $instance = $this->buildInstance();
        self::assertFalse($instance->hasMethod($classReflection, 'aMethodName'));
        self::assertFalse($instance->hasMethod($classReflection, 'aMethodName'));
    }

    public function testHasMethodNotImplementProxyAndNotImplementState(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturn(false);

        $instance = $this->buildInstance();
        self::assertFalse($instance->hasMethod($classReflection, 'aMethodName'));
        self::assertFalse($instance->hasMethod($classReflection, 'aMethodName'));
    }

    public function testHasMethodImplementProxyMethodInProxy(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $instance = $this->buildInstance();
        self::assertFalse($instance->hasMethod($classReflection, 'getAttribute'));
        self::assertFalse($instance->hasMethod($classReflection, 'getAttribute'));
    }

    public function testStatesListDeclaratoionReflectionError(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aMethod'));
    }

    public function testHasMethodImplementProxyMethodInState(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $instance = $this->buildInstance();
        self::assertTrue($instance->hasMethod($classReflection, 'getFormattedBody'));
        self::assertTrue($instance->hasMethod($classReflection, 'getFormattedBody'));
    }

    public function testHasMethodImplementProxyMethodNotExist(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aFakeMethodName'));
    }

    public function testHasMethodImplementStateProxyNotFound(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aMethod'));
    }

    public function testHasMethodImplementStateMethodInState(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        self::assertTrue($this->buildInstance()->hasMethod($classReflection, 'getFormattedBody'));
    }

    public function testHasMethodImplementStateMethodInProxy(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'getAttribute'));
    }

    public function testHasMethodImplementStateMethodNotExist(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aNonExistantMethod'));
    }

    public function testGetMethodIsInterface(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(true);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodNotImplementProxyAndNotImplementState(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturn(false);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementProxyMethodInProxy(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $this->buildInstance()->getMethod($classReflection, 'getAttribute');
    }

    public function testGetMethodImplementProxyMethodInState(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $classReflection->expects(self::any())
            ->method('getNativeReflection')
            ->willReturn(
                new ReflectionClass(
                    $this->createMock(BetterReflectionClass::class)
                )
            );

        self::assertInstanceOf(MethodReflection::class, $this->buildInstance()->getMethod($classReflection, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodNotExist(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $this->buildInstance()->getMethod($classReflection, 'notExistantMethod');
    }

    public function testGetMethodImplementProxyMethodClosureReturnedIsStatic(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $classReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $this->buildInstance()->getMethod($classReflection, 'returnStaticClosure');
    }

    public function testGetMethodImplementStateProxyNotFound(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementStateProxyNotImplement(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(\DateTime::class.'\\Foo');

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementStateMethodInState(): void
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())
            ->method('getNativeReflection')
            ->willReturn(
                new ReflectionClass(
                    $this->createMock(BetterReflectionClass::class)
                )
            );

        $instance = $this->buildInstance();
        self::assertInstanceOf(MethodReflection::class, $instance->getMethod($classReflection, 'getFormattedBody'));
        self::assertInstanceOf(MethodReflection::class, $instance->getMethod($classReflection, 'getFormattedBody'));
    }

    public function testGetMethodImplementStateMethodInProxy(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $this->buildInstance()->getMethod($classReflection, 'getAttribute');
    }

    public function testGetMethodImplementStateMethodNotExist(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $classReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $classReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $classReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $this->buildInstance()->getMethod($classReflection, 'notExistantMethod');
    }
}
