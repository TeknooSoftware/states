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
use Teknoo\States\Automated\Assertion\Property\IsNotInstanceOf;

/**
 * Class IsNotInstanceOfTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\Property\IsNotInstanceOf
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
class IsNotInstanceOfTest extends AbstractConstraintTest
{
    /**
     * @return IsNotInstanceOf|ConstraintInterface
     */
    public function buildInstance(): ConstraintInterface
    {
        return new IsNotInstanceOf(\DateTime::class);
    }

    public function testNotInstanceProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::once())->method('isValid')->with($value = new \stdClass())->willReturnSelf();

        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }

    public function testInstanceOfProperty()
    {
        $constraintSet = $this->createMock(ConstraintsSetInterface::class);
        $constraintSet->expects(self::never())->method('isValid');

        $value = new \DateTime();
        self::assertInstanceOf(
            ConstraintInterface::class,
            $this->buildInstance()->inConstraintSet($constraintSet)->check($value)
        );
    }
}
