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

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Assertion\Property\CountsLess;

/**
 * Class CountsLessTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\CountsLess
 * @covers \Teknoo\States\Automated\Assertion\Property\AbstractConstraint
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class CountsLessTest extends AbstractConstraintTests
{
    public function buildInstance(int $expected=0): ConstraintInterface
    {
        return new CountsLess($expected);
    }

    public function testValidArrayProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = [1,2])->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(3)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidArrayProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = [1,2];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(1)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testValidCountableProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = new \ArrayObject([1,2]))->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(3)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidCountableProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = new \ArrayObject([1,2]);
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(1)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidClassCountableProperty(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = new \stdClass();
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(2)->inConstraintSet($constraintSet)->check($value)
        );
    }
}
