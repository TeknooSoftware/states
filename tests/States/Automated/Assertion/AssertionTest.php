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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property;

/**
 * Class AssertionTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 * @covers \Teknoo\States\Automated\Assertion\Property
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class AssertionTest extends AbstractAssertionTest
{
    /**
     * @return Property
     */
    public function buildInstance()
    {
        return new Property(['state1', 'state2']);
    }

    public function testWithConstraint()
    {
        self::assertInstanceOf(
            Property::class,
            $this->buildInstance()->with('fooBar', $this->createMock(Property\ConstraintInterface::class))
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testWithBadPropertyName()
    {
        $this->buildInstance()->with(new \stdClass(), 42);
    }

    /**
     * @expectedException \TypeError
     */
    public function testWithBadConstraint()
    {
        $this->buildInstance()->with('fooBar', new \stdClass());
    }
}
