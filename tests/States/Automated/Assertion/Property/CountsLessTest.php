<?php

/**
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
use Teknoo\States\Automated\Assertion\Property\CountsLess;

/**
 * Class CountsLessTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\CountsLess
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
class CountsLessTest extends AbstractConstraintTest
{
    /**
     * @return CountsLess|ConstraintInterface
     */
    public function buildInstance(int $expected=0): ConstraintInterface
    {
        return new CountsLess($expected);
    }

    public function testValidArrayProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = [1,2])->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(3)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidArrayProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = [1,2];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(1)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testValidCountableProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = new \ArrayObject([1,2]))->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(3)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidCountableProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = new \ArrayObject([1,2]);
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance(1)->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testNotValidClassCountableProperty()
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
