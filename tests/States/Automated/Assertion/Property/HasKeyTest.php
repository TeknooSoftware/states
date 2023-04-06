<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
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
use Teknoo\States\Automated\Assertion\Property\HasKey;

/**
 * Class IsNotEmptyTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\HasKey
 * @covers \Teknoo\States\Automated\Assertion\Property\AbstractConstraint
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class HasKeyTest extends AbstractConstraintTests
{
    public function buildInstance(): ConstraintInterface
    {
        return new HasKey('foo');
    }

    public function testNotArray(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = 'foo';
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyNotExist(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = [];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyExistButEmptyValue(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid');

        $value = ['foo' => null];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyExistAndNotEmptyValue(): void
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = ['foo' => 'bar'])->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }
}
