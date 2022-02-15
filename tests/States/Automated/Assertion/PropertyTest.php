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

namespace Teknoo\Tests\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Exception\IllegalArgument;

/**
 * Class AssertionTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 * @covers \Teknoo\States\Automated\Assertion\Property
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class PropertyTest extends AbstractAssertionTest
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
            $this->buildInstance()->with(
                'fooBar',
                $this->createMock(Property\ConstraintInterface::class)
            )
        );
    }

    public function testWithBadPropertyName()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->with(new \stdClass(), 42);
    }

    public function testWithBadConstraint()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->with('fooBar', new \stdClass());
    }

    public function testCheckWithNoConstraintIsValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::exactly(2))
            ->method('enableState')
            ->withConsecutive(['state1'], ['state2'])
            ->willReturnSelf();

        $assertionChecked = $assertion->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion, $assertionChecked);
    }

    public function testCheckWithInvalidPropertyNameInConstraint()
    {
        $assertion = $this->buildInstance();

        $this->expectException(IllegalArgument::class);
        $assertion->with('123', new Property\IsNotNull());
    }

    public function testCheckOneConstraintIsValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::exactly(2))
            ->method('enableState')
            ->withConsecutive(['state1'], ['state2'])
            ->willReturnSelf();

        $proxy->expects(self::once())
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use ($proxy) {
                self::assertEquals('prop1', $name);
                $value = 123;
                $set->check($value);

                return $proxy;
            });

        $assertion2 = $assertion->with('prop1', new Property\IsNotNull());

        self::assertInstanceOf(Property::class, $assertion2);
        self::assertNotSame($assertion, $assertion2);

        $assertionChecked = $assertion2->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion2, $assertionChecked);
    }

    public function testCheckOneConstraintIsNotValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::never())
            ->method('enableState');

        $proxy->expects(self::once())
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use ($proxy) {
                self::assertEquals('prop1', $name);
                $var = null;
                $set->check($var);

                return $proxy;
            });

        $assertion2 = $assertion->with('prop1', new Property\IsNotNull());

        self::assertInstanceOf(Property::class, $assertion2);
        self::assertNotSame($assertion, $assertion2);

        $assertionChecked = $assertion2->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion2, $assertionChecked);
    }

    public function testCheckTwoPropertyConstraintIsValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::exactly(2))
            ->method('enableState')
            ->withConsecutive(['state1'], ['state2'])
            ->willReturnSelf();

        $counter=0;
        $proxy->expects(self::exactly(2))
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use (&$counter, $proxy) {
                if (0 === $counter++) {
                    self::assertEquals('prop1', $name);
                } else {
                    self::assertEquals('prop2', $name);
                }

                $value = 123;
                $set->check($value);

                return $proxy;
            });

        $assertion2 = $assertion->with('prop1', new Property\IsNotNull());
        $assertion3 = $assertion2->with('prop2', new Property\IsEqual(123));

        self::assertInstanceOf(Property::class, $assertion2);
        self::assertNotSame($assertion, $assertion2);
        self::assertInstanceOf(Property::class, $assertion3);
        self::assertNotSame($assertion2, $assertion3);

        $assertionChecked = $assertion3->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion3, $assertionChecked);
    }

    public function testCheckTwoPropertyOneNotValidSoNotValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::never())
            ->method('enableState');

        $counter=0;
        $proxy->expects(self::exactly(2))
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use (&$counter, $proxy) {
                if (0 === $counter++) {
                    self::assertEquals('prop1', $name);
                } else {
                    self::assertEquals('prop2', $name);
                }

                $var = 456;
                $set->check($var);

                return $proxy;
            });

        $assertion2 = $assertion->with('prop1', new Property\IsNotNull());
        $assertion3 = $assertion2->with('prop2', new Property\IsEqual(123));

        self::assertInstanceOf(Property::class, $assertion2);
        self::assertNotSame($assertion, $assertion2);
        self::assertInstanceOf(Property::class, $assertion3);
        self::assertNotSame($assertion2, $assertion3);

        $assertionChecked = $assertion3->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion3, $assertionChecked);
    }
}
