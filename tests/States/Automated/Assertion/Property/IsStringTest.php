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

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Assertion\Property\IsString;

/**
 * Class IsNotEmptyTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\IsString
 * @covers \Teknoo\States\Automated\Assertion\Property\AbstractConstraint
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class IsStringTest extends AbstractConstraintTests
{
    public function buildInstance(): ConstraintInterface
    {
        return new IsString();
    }

    public function testIsStringProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid');

        $value = 'foo';
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testIsNumericProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid')->with($value = 123)->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testIsBoolProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid')->with($value = true)->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testIsArrayProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid')->with($value = [])->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testIsObjectProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid')->with($value = new \stdClass())->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }
}
