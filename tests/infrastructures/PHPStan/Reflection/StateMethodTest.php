<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Reflection;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionNamedType;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionParameter;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionNamedType as BetterReflectionNamedType;
use PHPStan\BetterReflection\Reflection\ReflectionParameter as BetterReflectionParameter;
use PHPStan\TrinaryLogic;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Reflection\StateMethod;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\PHPStan\Reflection\StateMethod
 */
class StateMethodTest extends TestCase
{
    protected function buildInstance($doc = 'factory doc'): \Teknoo\States\PHPStan\Reflection\StateMethod
    {
        $factoryReflection = $this->createMock(BetterReflectionMethod::class);
        $factoryReflection->expects($this->any())->method('getName')->willReturn('factory');
        $factoryReflection->expects($this->any())->method('getFileName')->willReturn('factory.php');
        $factoryReflection->expects($this->never())->method('getStartLine');
        $factoryReflection->expects($this->never())->method('getEndLine');
        $factoryReflection->expects($this->any())->method('getDocComment')->willReturn($doc);
        $factoryReflection->expects($this->any())->method('isStatic')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isPrivate')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isPublic')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isDeprecated')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isFinal')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isInternal')->willReturn(false);
        $factoryReflection->expects($this->any())->method('isAbstract')->willReturn(false);
        $factoryReflection->expects($this->never())->method('isVariadic');
        $factoryReflection->expects($this->never())->method('getReturnType');
        $factoryReflection->expects($this->never())->method('getParameters');

        $closureReflection = $this->createMock(BetterReflectionFunction::class);
        $closureReflection->expects($this->never())->method('getName');
        $closureReflection->expects($this->never())->method('getFileName');
        $closureReflection->expects($this->any())->method('getStartLine')->willReturn(12);
        $closureReflection->expects($this->any())->method('getEndLine')->willReturn(34);
        $closureReflection->expects($this->never())->method('getDocComment');
        $closureReflection->expects($this->any())->method('isVariadic')->willReturn(false);
        $closureReflection->expects($this->any())->method('returnsReference')->willReturn(false);
        $closureReflection->expects($this->any())->method('getReturnType')->willReturn(
            $this->createMock(BetterReflectionNamedType::class)
        );

        $closureReflection->expects($this->any())->method('getParameters')->willReturn([
            $this->createMock(BetterReflectionParameter::class)
        ]);

        return new StateMethod(
            new ReflectionMethod($factoryReflection),
            new ReflectionFunction($closureReflection),
            new ReflectionClass($this->createMock(BetterReflectionClass::class)),
        );
    }

    public function testGetName(): void
    {
        self::assertEquals('factory', $this->buildInstance()->getName());
    }

    public function testGetReflection(): void
    {
        self::assertInstanceOf(ReflectionMethod::class, $this->buildInstance()->getReflection());
    }

    public function testGetFileName(): void
    {
        self::assertEquals('factory.php', $this->buildInstance()->getFileName());
    }

    public function testGetDeclaringClass(): void
    {
        self::assertInstanceOf(\ReflectionClass::class, $this->buildInstance()->getDeclaringClass());
    }

    public function testGetStartLine(): void
    {
        self::assertEquals(12, $this->buildInstance()->getStartLine());
    }

    public function testGetEndLine(): void
    {
        self::assertEquals(34, $this->buildInstance()->getEndLine());
    }

    public function testGetDocComment(): void
    {
        self::assertEquals('factory doc', $this->buildInstance()->getDocComment());
    }

    public function testGetDocCommentNull(): void
    {
        self::assertNotEquals(1, ini_get('zend.assertions'));
        self::assertEmpty($this->buildInstance('')->getDocComment());
    }

    public function testIsStatic(): void
    {
        self::assertFalse($this->buildInstance()->isStatic());
    }

    public function testIsPrivate(): void
    {
        self::assertFalse($this->buildInstance()->isPrivate());
    }

    public function testIsPublic(): void
    {
        self::assertFalse($this->buildInstance()->isPublic());
    }

    public function testGetPrototype(): void
    {
        self::assertInstanceOf(StateMethod::class, $this->buildInstance()->getPrototype());
    }

    public function testIsDeprecated(): void
    {
        self::assertEquals(TrinaryLogic::createFromBoolean(false), $this->buildInstance()->isDeprecated());
    }

    public function testIsFinal(): void
    {
        self::assertFalse($this->buildInstance()->isFinal());
    }

    public function testIsInternal(): void
    {
        self::assertFalse($this->buildInstance()->isInternal());
    }

    public function testIsAbstract(): void
    {
        self::assertFalse($this->buildInstance()->isAbstract());
    }

    public function testIsVariadic(): void
    {
        self::assertFalse($this->buildInstance()->isVariadic());
    }

    public function testGetReturnType(): void
    {
        self::assertInstanceOf(ReflectionNamedType::class, $this->buildInstance()->getReturnType());
    }

    public function testGetTentativeReturnType(): void
    {
        self::assertNull($this->buildInstance()->getTentativeReturnType());
    }

    public function testGetParameters(): void
    {
        self::assertInstanceOf(
            ReflectionParameter::class,
            current($this->buildInstance()->getParameters()),
        );
    }

    public function testReturnsByReference(): void
    {
        self::assertEquals(
            TrinaryLogic::createNo(),
            $this->buildInstance()->returnsByReference(),
        );
    }
}
