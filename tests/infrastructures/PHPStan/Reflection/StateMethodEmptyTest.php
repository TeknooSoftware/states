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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\PHPStan\Reflection\StateMethod
 */
class StateMethodEmptyTest extends TestCase
{
    protected function buildInstance($doc = 'factory doc', $closureScopeClass = null)
    {
        $factoryReflection = $this->createMock(\ReflectionMethod::class);
        $factoryReflection->expects(self::any())->method('getName')->willReturn('factory');
        $factoryReflection->expects(self::any())->method('getFileName')->willReturn(false);
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
        $closureReflection->expects(self::any())->method('getStartLine')->willReturn(false);
        $closureReflection->expects(self::any())->method('getEndLine')->willReturn(false);
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

    public function testGetFileName()
    {
        self::assertNull($this->buildInstance()->getFileName());
    }

    public function testGetStartLine()
    {
        self::assertNull($this->buildInstance()->getStartLine());
    }

    public function testGetEndLine()
    {
        self::assertNull($this->buildInstance()->getEndLine());
    }
}
