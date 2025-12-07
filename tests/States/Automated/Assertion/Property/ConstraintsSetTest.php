<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSet;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(ConstraintsSet::class)]
class ConstraintsSetTest extends TestCase
{
    private $set;

    public function buildInstance($constraints, $propery): ConstraintsSet
    {
        return new ConstraintsSet($constraints, $propery);
    }

    public function testConstructorWithBadConstraintsArray(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance(new \stdClass(), $this->createStub(Property::class));
    }

    public function testConstructorWithBadProperty(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance([$this->createStub(ConstraintInterface::class)], new \stdClass());
    }

    public function testCheckWithoutConstraintMustValid(): void
    {
        $property = $this->createMock(Property::class);
        $property->expects($this->once())->method('isValid');

        $value = 'foo';

        $this->assertInstanceOf(ConstraintsSet::class, $this->buildInstance([], $property)->check($value));
    }

    public function testIsValidWithoutConstraintMustValid(): void
    {
        $property = $this->createMock(Property::class);
        $property->expects($this->once())->method('isValid');

        $value = 'foo';

        $this->assertInstanceOf(ConstraintsSet::class, $this->buildInstance([], $property)->isValid($value));
    }

    public function testProcessWithTwoConstraintOnlyOneValidSoPropertyIsNotValid(): void
    {
        $property = $this->createMock(Property::class);
        $property->expects($this->never())->method('isValid');

        $value = 'foo';

        $set = null;
        $constraint1 = $this->createMock(ConstraintInterface::class);
        $constraint1->expects($this->once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint1): MockObject&ConstraintInterface {
                $this->set = $cs;

                return $constraint1;
            });

        $constraint1->expects($this->once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint1): MockObject&ConstraintInterface {
                $this->assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint1;
            });

        $constraint2 = $this->createMock(ConstraintInterface::class);
        $constraint2->expects($this->once())
            ->method('inConstraintSet')
            ->willReturnSelf();

        $this->assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([$constraint1, $constraint2], $property)->isValid($value),
        );
    }

    public function testProcessWithTwoConstraintTwoValidSoPropertyIsValid(): void
    {
        $property = $this->createMock(Property::class);
        $property->expects($this->once())->method('isValid');

        $value = 'foo';
        $constraint1 = $this->createMock(ConstraintInterface::class);
        $constraint1->expects($this->once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint1): MockObject&ConstraintInterface {
                $this->set = $cs;

                return $constraint1;
            });

        $constraint1->expects($this->once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint1): MockObject&ConstraintInterface {
                $this->assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint1;
            });

        $constraint2 = $this->createMock(ConstraintInterface::class);
        $constraint2->expects($this->once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint2): MockObject&ConstraintInterface {
                $this->set = $cs;

                return $constraint2;
            });

        $constraint2->expects($this->once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint2): MockObject&ConstraintInterface {
                $this->assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint2;
            });

        $this->assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([$constraint1, $constraint2], $property)->isValid($value),
        );
    }
}
