<?php

/*
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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan;

use PHPStan\Broker\Broker;
use PHPStan\Cache\Cache;
use PHPStan\Parser\Parser;
use PHPStan\Parser\FunctionCallStatementFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\ShouldNotHappenException;
use Teknoo\States\PHPStan\MethodsClassExtension;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Draft;
use Teknoo\Tests\Support\Article\Article\Published;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\PHPStan\MethodsClassExtension
 */
class MethodsClassExtensionTest extends TestCase
{
    private ?Broker $broker = null;

    private ?Parser $parser = null;

    private ?FunctionCallStatementFinder $functionCallStatementFinder = null;

    private ?Cache $cache = null;

    private function getBrokerMock(): Broker
    {
        if (!$this->broker instanceof Broker) {
            $this->broker = $this->createMock(Broker::class);
        }

        return $this->broker;
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
        $instance = new MethodsClassExtension(
            $this->getParserMock(),
            $this->getFunctionCallStatementFinderMock(),
            $this->getCacheMock()
        );

        $instance->setBroker($this->getBrokerMock());

        return $instance;
    }

    public function testHasMethodIsInterface()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(true);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aMethodName'));
    }

    public function testHasMethodNotImplementProxyAndNotImplementState()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturn(false);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aMethodName'));
    }

    public function testHasMethodImplementProxyMethodInProxy()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'getAttribute'));
    }

    public function testHasMethodImplementProxyMethodInState()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertTrue($this->buildInstance()->hasMethod($classReflection, 'getFormattedBody'));
    }

    public function testHasMethodImplementProxyMethodNotExist()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aFakeMethodName'));
    }

    public function testHasMethodImplementStateProxyNotFound()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aMethod'));
    }

    public function testHasMethodImplementStateMethodInState()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertTrue($this->buildInstance()->hasMethod($classReflection, 'getFormattedBody'));
    }

    public function testHasMethodImplementStateMethodInProxy()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'getAttribute'));
    }

    public function testHasMethodImplementStateMethodNotExist()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertFalse($this->buildInstance()->hasMethod($classReflection, 'aNonExistantMethod'));
    }

    public function testGetMethodIsInterface()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(true);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodNotImplementProxyAndNotImplementState()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturn(false);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementProxyMethodInProxy()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'getAttribute');
    }

    public function testGetMethodImplementProxyMethodInState()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);
        $nativeReflection->expects(self::any())->method('newInstanceWithoutConstructor')->willReturn($this->createMock(Article::class));

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertInstanceOf(MethodReflection::class, $this->buildInstance()->getMethod($classReflection, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxystatesListDeclarationNotAVailable()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(null);
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'notExistantMethod');
    }


    public function testGetMethodImplementProxyMethodNotExist()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Article::class);

        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $reflectionMethod->expects(self::any())->method('getClosure')->willReturn(function () {
            return [
                Draft::class,
                Published::class,
            ];
        });
        $nativeReflection->expects(self::any())->method('getMethod')->willReturnMap([
            ['statesListDeclaration', $reflectionMethod]
        ]);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'notExistantMethod');
    }

    public function testGetMethodImplementStateProxyNotFound()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementStateProxyNotImplement()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(\DateTime::class.'\\Foo');

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'aMethod');
    }

    public function testGetMethodImplementStateMethodInState()
    {
        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        self::assertInstanceOf(MethodReflection::class, $this->buildInstance()->getMethod($classReflection, 'getFormattedBody'));
    }

    public function testGetMethodImplementStateMethodInProxy()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'getAttribute');
    }

    public function testGetMethodImplementStateMethodNotExist()
    {
        $this->expectException(ShouldNotHappenException::class);

        $classReflection = $this->createMock(ClassReflection::class);
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('isInterface')->willReturn(false);
        $nativeReflection->expects(self::any())->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);

        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);

        $this->buildInstance()->getMethod($classReflection, 'notExistantMethod');
    }
}
