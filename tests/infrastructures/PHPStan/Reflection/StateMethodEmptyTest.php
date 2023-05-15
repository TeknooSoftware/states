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
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionParameter;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\TrinaryLogic;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use ReflectionIntersectionType as NativeReflectionIntersectionType;
use ReflectionNamedType as NativeReflectionNamedType;
use ReflectionUnionType as NativeReflectionUnionType;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\PHPStan\Reflection\StateMethod
 */
class StateMethodEmptyTest extends TestCase
{
    protected function buildInstance($doc = 'factory doc', $closureScopeClass = null): \Teknoo\States\PHPStan\Reflection\StateMethod
    {
        $factoryReflection = $this->createMock(\ReflectionMethod::class);
        $factoryReflection->expects(self::any())->method('getName')->willReturn('factory');
        $factoryReflection->expects(self::any())->method('getFileName')->willReturn('');
        $factoryReflection->expects(self::never())->method('getStartLine');
        $factoryReflection->expects(self::never())->method('getEndLine');
        $factoryReflection->expects(self::any())->method('getDocComment')->willReturn($doc);
        $factoryReflection->expects(self::any())->method('isStatic')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isPrivate')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isPublic')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isDeprecated')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isFinal')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isInternal')->willReturn(false);
        $factoryReflection->expects(self::any())->method('isAbstract')->willReturn(false);
        $factoryReflection->expects(self::never())->method('isVariadic');
        $factoryReflection->expects(self::never())->method('getReturnType');
        $factoryReflection->expects(self::never())->method('getParameters');

        $closureReflection = $this->createMock(\ReflectionFunction::class);
        $closureReflection->expects(self::never())->method('getName');
        $closureReflection->expects(self::never())->method('getFileName');
        $closureReflection->expects(self::any())->method('getStartLine')->willReturn(0);
        $closureReflection->expects(self::any())->method('getEndLine')->willReturn(0);
        $closureReflection->expects(self::never())->method('getDocComment');
        $closureReflection->expects(self::any())->method('isVariadic')->willReturn(false);
        $closureReflection->expects(self::any())->method('returnsReference')->willReturn(true);
        $closureReflection->expects(self::any())->method('getReturnType')->willReturn(
            $type = $this->createMock(\ReflectionType::class)
        );
        $closureReflection->expects(self::any())->method('getParameters')->willReturn([
            $p1 = $this->createMock(\ReflectionParameter::class),
            $p2 = $this->createMock(\ReflectionParameter::class),
            $p3 = $this->createMock(\ReflectionParameter::class),
        ]);

        $p1->expects(self::any())
            ->method('getType')
            ->willReturn($rf1 = $this->createMock(NativeReflectionIntersectionType::class));

        $rf1->expects(self::any())
            ->method('allowsNull')
            ->willReturn(true);

        $rf1->expects(self::any())
            ->method('getTypes')
            ->willReturn([
                $rf11 = $this->createMock(NativeReflectionNamedType::class),
                $rf12 = $this->createMock(NativeReflectionNamedType::class),
            ]);

        $rf11->expects(self::any())
            ->method('getName')
            ->willReturn('pt11');

        $rf12->expects(self::any())
            ->method('getName')
            ->willReturn('pt12');

        $p1->expects(self::any())
            ->method('isOptional')
            ->willReturn(false);

        $p2->expects(self::any())
            ->method('getType')
            ->willReturn($rf2 = $this->createMock(NativeReflectionUnionType::class));

        $rf2->expects(self::any())
            ->method('allowsNull')
            ->willReturn(true);

        $rf2->expects(self::any())
            ->method('getTypes')
            ->willReturn([
                $rf21 = $this->createMock(NativeReflectionNamedType::class),
                $rf22 = $this->createMock(NativeReflectionNamedType::class),
            ]);

        $rf21->expects(self::any())
            ->method('getName')
            ->willReturn('pt21');

        $rf22->expects(self::any())
            ->method('getName')
            ->willReturn('pt22');

        $p2->expects(self::any())
            ->method('isOptional')
            ->willReturn(false);

        $p3->expects(self::any())
            ->method('getType')
            ->willReturn($rf3 = $this->createMock(NativeReflectionNamedType::class));

        $rf3->expects(self::any())
            ->method('allowsNull')
            ->willReturn(true);

        $rf3->expects(self::any())
            ->method('getName')
            ->willReturn('pt3');

        $p3->expects(self::any())
            ->method('getDefaultValue')
            ->willReturn('foo');

        $p3->expects(self::any())
            ->method('isOptional')
            ->willReturn(true);

        return new StateMethod(
            $factoryReflection,
            $closureReflection,
            new ReflectionClass($this->createMock(BetterReflectionClass::class)),
        );
    }

    public function testGetReflection(): void
    {
        self::assertNull($this->buildInstance()->getReflection());
    }

    public function testGetFileName(): void
    {
        self::assertNull($this->buildInstance()->getFileName());
    }

    public function testGetStartLine(): void
    {
        self::assertNull($this->buildInstance()->getStartLine());
    }

    public function testGetEndLine(): void
    {
        self::assertNull($this->buildInstance()->getEndLine());
    }

    public function testGetReturnType(): void
    {
        self::assertNull($this->buildInstance()->getReturnType());
    }

    public function testGetParameters(): void
    {
        assert_options(ASSERT_ACTIVE, 0);
        self::assertInstanceOf(
            ReflectionParameter::class,
            $this->buildInstance()->getParameters()[0]
        );
        assert_options(ASSERT_ACTIVE, 1);
    }

    public function testGetDocCommentNull(): void
    {
        assert_options(ASSERT_ACTIVE, 0);
        self::assertEmpty($this->buildInstance('')->getDocComment());
        self::assertEmpty($this->buildInstance(false)->getDocComment());
        assert_options(ASSERT_ACTIVE, 1);
    }

    public function testReturnsByReference(): void
    {
        self::assertEquals(
            TrinaryLogic::createYes(),
            $this->buildInstance()->returnsByReference(),
        );
    }
}
