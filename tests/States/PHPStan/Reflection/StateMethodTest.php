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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Reflection;

use PHPStan\TrinaryLogic;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Reflection\StateMethod;

/**
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
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
    protected function buildInstance($doc = 'factory doc', $closureScopeClass = null)
    {
        $factoryReflection = $this->createMock(\ReflectionMethod::class);
        $factoryReflection->expects(self::any())->method('getName')->willReturn('factory');
        $factoryReflection->expects(self::any())->method('getFileName')->willReturn('factory.php');
        $factoryReflection->expects(self::never())->method('getClosureScopeClass');
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
        $closureReflection->expects(self::any())->method('getClosureScopeClass')->willReturn(
            $closureScopeClass ?? $this->createMock(\ReflectionClass::class)
        );
        $closureReflection->expects(self::any())->method('getStartLine')->willReturn(12);
        $closureReflection->expects(self::any())->method('getEndLine')->willReturn(34);
        $closureReflection->expects(self::never())->method('getDocComment');
        $closureReflection->expects(self::any())->method('isVariadic')->willReturn(false);
        $closureReflection->expects(self::any())->method('getReturnType')->willReturn(
            $type = $this->createMock(\ReflectionType::class)
        );
        $closureReflection->expects(self::any())->method('getParameters')->willReturn([
            $p1 = $this->createMock(\ReflectionParameter::class)
        ]);

        return new StateMethod($factoryReflection, $closureReflection);
    }

    public function testGetName()
    {
        self::assertEquals('factory', $this->buildInstance()->getName());
    }

    public function testGetFileName()
    {
        self::assertEquals('factory.php', $this->buildInstance()->getFileName());
    }

    public function testGetDeclaringClass()
    {
        self::assertInstanceOf(\ReflectionClass::class, $this->buildInstance()->getDeclaringClass());
    }

    public function testGetDeclaringClassError()
    {
        $this->expectException(\RuntimeException::class);
        $this->buildInstance('foo', false)->getDeclaringClass();
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
        self::assertNull($this->buildInstance(false)->getDocComment());
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
        self::assertInstanceOf(\ReflectionType::class, $this->buildInstance()->getReturnType());
    }

    public function testGetParameters()
    {
        self::assertEquals(
            [$p1 = $this->createMock(\ReflectionParameter::class)],
            $this->buildInstance()->getParameters()
        );
    }
}