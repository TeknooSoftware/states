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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Assertion\Property\HasNotEmptyValueForKey;

/**
 * Class IsNotEmptyTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\HasNotEmptyValueForKey
 * @covers \Teknoo\States\Automated\Assertion\Property\AbstractConstraint
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class HasNotEmptyValueForKeyTest extends AbstractConstraintTest
{
    /**
     * @return HasNotEmptyValueForKey|ConstraintInterface
     */
    public function buildInstance(): ConstraintInterface
    {
        return new HasNotEmptyValueForKey('foo');
    }

    public function testNotArray()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = 'foo';
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyNotExist()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = [];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyExistButEmptyValue()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = ['foo' => null];
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testKeyExistAndNotEmptyValue()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = ['foo' => 'bar'])->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }
}
