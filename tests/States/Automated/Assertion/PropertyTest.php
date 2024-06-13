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

namespace Teknoo\Tests\States\Automated\Assertion;

use PHPUnit\Framework\Attributes\CoversClass;
use Teknoo\States\Automated\Assertion\AbstractAssertion;
use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Exception\IllegalArgument;

/**
 * Class AssertionTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(AbstractAssertion::class)]
#[CoversClass(Property::class)]
class PropertyTest extends AbstractAssertionTests
{
    public function buildInstance(): \Teknoo\States\Automated\Assertion\Property
    {
        return new Property(['state1', 'state2']);
    }

    public function testWithConstraint(): void
    {
        self::assertInstanceOf(
            Property::class,
            $this->buildInstance()->with(
                'fooBar',
                $this->createMock(Property\ConstraintInterface::class)
            )
        );
    }

    public function testWithBadPropertyName(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->with(new \stdClass(), 42);
    }

    public function testWithBadConstraint(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->with('fooBar', new \stdClass());
    }

    public function testCheckWithNoConstraintIsValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects($this->exactly(2))
            ->method('enableState')
            ->with($this->callback(
                fn ($value) => match ($value) {
                    'state1' => true,
                    'state2' => true,
                    default => false,
                }
            ))
            ->willReturnSelf();

        $assertionChecked = $assertion->check($proxy);

        self::assertInstanceOf(Property::class, $assertionChecked);
        self::assertNotSame($assertion, $assertionChecked);
    }

    public function testCheckWithInvalidPropertyNameInConstraint(): void
    {
        $assertion = $this->buildInstance();

        $this->expectException(IllegalArgument::class);
        $assertion->with('123', new Property\IsNotNull());
    }

    public function testCheckOneConstraintIsValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects($this->exactly(2))
            ->method('enableState')
            ->with($this->callback(
                fn ($value) => match ($value) {
                    'state1' => true,
                    'state2' => true,
                    default => false,
                }
            ))
            ->willReturnSelf();

        $proxy->expects($this->once())
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use ($proxy): \PHPUnit\Framework\MockObject\MockObject&\Teknoo\States\Automated\AutomatedInterface {
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

    public function testCheckOneConstraintIsNotValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects($this->never())
            ->method('enableState');

        $proxy->expects($this->once())
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use ($proxy): \PHPUnit\Framework\MockObject\MockObject&\Teknoo\States\Automated\AutomatedInterface {
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

    public function testCheckTwoPropertyConstraintIsValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects($this->exactly(2))
            ->method('enableState')
            ->with($this->callback(
                fn ($value) => match ($value) {
                    'state1' => true,
                    'state2' => true,
                    default => false,
                }
            ))
            ->willReturnSelf();

        $counter = 0;
        $proxy->expects($this->exactly(2))
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use (&$counter, $proxy): \PHPUnit\Framework\MockObject\MockObject&\Teknoo\States\Automated\AutomatedInterface {
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

    public function testCheckTwoPropertyOneNotValidSoNotValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects($this->never())
            ->method('enableState');

        $counter = 0;
        $proxy->expects($this->exactly(2))
            ->method('checkProperty')
            ->willReturnCallback(function ($name, ConstraintsSetInterface $set) use (&$counter, $proxy): \PHPUnit\Framework\MockObject\MockObject&\Teknoo\States\Automated\AutomatedInterface {
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
