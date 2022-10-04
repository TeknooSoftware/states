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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\PHPStan\Reflection\StateMethod
 */
class StateMethodTest extends TestCase
{
    protected function buildInstance($doc = 'factory doc')
    {
        $factoryReflection = $this->createMock(BetterReflectionMethod::class);
        $factoryReflection->expects(self::any())->method('getName')->willReturn('factory');
        $factoryReflection->expects(self::any())->method('getFileName')->willReturn('factory.php');
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

        $closureReflection = $this->createMock(BetterReflectionFunction::class);
        $closureReflection->expects(self::never())->method('getName');
        $closureReflection->expects(self::never())->method('getFileName');
        $closureReflection->expects(self::any())->method('getStartLine')->willReturn(12);
        $closureReflection->expects(self::any())->method('getEndLine')->willReturn(34);
        $closureReflection->expects(self::never())->method('getDocComment');
        $closureReflection->expects(self::any())->method('isVariadic')->willReturn(false);
        $closureReflection->expects(self::any())->method('getReturnType')->willReturn(
            $this->createMock(BetterReflectionNamedType::class)
        );

        $closureReflection->expects(self::any())->method('getParameters')->willReturn([
            $this->createMock(BetterReflectionParameter::class)
        ]);

        return new StateMethod(
            new ReflectionMethod($factoryReflection),
            new ReflectionFunction($closureReflection),
            new ReflectionClass($this->createMock(BetterReflectionClass::class)),
        );
    }

    public function testGetName()
    {
        self::assertEquals('factory', $this->buildInstance()->getName());
    }

    public function testGetReflection()
    {
        self::assertInstanceOf(ReflectionMethod::class, $this->buildInstance()->getReflection());
    }

    public function testGetFileName()
    {
        self::assertEquals('factory.php', $this->buildInstance()->getFileName());
    }

    public function testGetDeclaringClass()
    {
        self::assertInstanceOf(\ReflectionClass::class, $this->buildInstance()->getDeclaringClass());
    }

    public function testGetStartLine()
    {
        self::assertEquals(12, $this->buildInstance()->getStartLine());
    }

    public function testGetEndLine()
    {
        self::assertEquals(34, $this->buildInstance()->getEndLine());
    }

    public function testGetDocComment()
    {
        self::assertEquals('factory doc', $this->buildInstance()->getDocComment());
    }

    public function testGetDocCommentNull()
    {
        ini_set('assert.active', 0);
        assert_options(ASSERT_ACTIVE, 0);
        assert_options(ASSERT_WARNING, 0);
        assert_options(ASSERT_ACTIVE, 0);
        self::assertEmpty($this->buildInstance('')->getDocComment());
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_BAIL, 1);
        assert_options(ASSERT_WARNING, 1);
    }

    public function testIsStatic()
    {
        self::assertFalse($this->buildInstance()->isStatic());
    }

    public function testIsPrivate()
    {
        self::assertFalse($this->buildInstance()->isPrivate());
    }

    public function testIsPublic()
    {
        self::assertFalse($this->buildInstance()->isPublic());
    }

    public function testGetPrototype()
    {
        self::assertInstanceOf(StateMethod::class, $this->buildInstance()->getPrototype());
    }

    public function testIsDeprecated()
    {
        self::assertEquals(TrinaryLogic::createFromBoolean(false), $this->buildInstance()->isDeprecated());
    }

    public function testIsFinal()
    {
        self::assertFalse($this->buildInstance()->isFinal());
    }

    public function testIsInternal()
    {
        self::assertFalse($this->buildInstance()->isInternal());
    }

    public function testIsAbstract()
    {
        self::assertFalse($this->buildInstance()->isAbstract());
    }

    public function testIsVariadic()
    {
        self::assertFalse($this->buildInstance()->isVariadic());
    }

    public function testGetReturnType()
    {
        self::assertInstanceOf(ReflectionNamedType::class, $this->buildInstance()->getReturnType());
    }

    public function testGetTentativeReturnType()
    {
        self::assertNull($this->buildInstance()->getTentativeReturnType());
    }

    public function testGetParameters()
    {
        self::assertInstanceOf(
            ReflectionParameter::class,
            current($this->buildInstance()->getParameters()),
        );
    }
}
