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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSet;

/**
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\Automated\Assertion\Property\ConstraintsSet
 */
class ConstraintsSetTest extends \PHPUnit\Framework\TestCase
{
    private $set;

    public function buildInstance($constraints, $propery): ConstraintsSet
    {
        return new ConstraintsSet($constraints, $propery);
    }

    public function testConstructorWithBadConstraintsArray()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance(new \stdClass(), $this->createMock(Property::class));
    }

    public function testConstructorWithBadProperty()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance([$this->createMock(Property\ConstraintInterface::class)], new \stdClass());
    }

    public function testCheckWithoutConstraintMustValid()
    {
        $property = $this->createMock(Property::class);
        $property->expects(self::once())->method('isValid');

        $value = 'foo';

        self::assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([], $property)->check($value)
        );
    }

    public function testIsValidWithoutConstraintMustValid()
    {
        $property = $this->createMock(Property::class);
        $property->expects(self::once())->method('isValid');

        $value = 'foo';

        self::assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([], $property)->isValid($value)
        );
    }

    public function testProcessWithTwoConstraintOnlyOneValidSoPropertyIsNotValid()
    {
        $property = $this->createMock(Property::class);
        $property->expects(self::never())->method('isValid');

        $value = 'foo';

        $set = null;
        $constraint1 = $this->createMock(ConstraintInterface::class);
        $constraint1->expects(self::once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint1) {
                $this->set = $cs;

                return $constraint1;
            });

        $constraint1->expects(self::once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint1) {
                self::assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint1;
            });

        $constraint2 = $this->createMock(ConstraintInterface::class);
        $constraint2->expects(self::once())
            ->method('inConstraintSet')
            ->willReturnSelf();

        self::assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([$constraint1, $constraint2], $property)->isValid($value)
        );
    }

    public function testProcessWithTwoConstraintTwoValidSoPropertyIsValid()
    {
        $property = $this->createMock(Property::class);
        $property->expects(self::once())->method('isValid');

        $value = 'foo';

        $set = null;
        $constraint1 = $this->createMock(ConstraintInterface::class);
        $constraint1->expects(self::once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint1) {
                $this->set = $cs;

                return $constraint1;
            });

        $constraint1->expects(self::once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint1) {
                self::assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint1;
            });

        $constraint2 = $this->createMock(ConstraintInterface::class);
        $constraint2->expects(self::once())
            ->method('inConstraintSet')
            ->willReturnCallback(function ($cs) use ($constraint2) {
                $this->set = $cs;

                return $constraint2;
            });

        $constraint2->expects(self::once())
            ->method('check')
            ->willReturnCallback(function ($value) use ($constraint2) {
                self::assertInstanceOf(ConstraintsSet::class, $this->set);
                $this->set->isValid($value);

                return $constraint2;
            });

        self::assertInstanceOf(
            ConstraintsSet::class,
            $this->buildInstance([$constraint1, $constraint2], $property)->isValid($value)
        );
    }
}