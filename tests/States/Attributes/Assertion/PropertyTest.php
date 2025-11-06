<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Attributes\Assertion;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Attributes\Assertion\Property;
use Teknoo\States\Automated\Assertion\Property as PropertyAssertion;
use Teknoo\States\Automated\Assertion\Property\IsEqual;
use Teknoo\States\Automated\Assertion\Property\IsNotNull;
use Teknoo\Tests\Support\MockProxy;
use Teknoo\Tests\Support\States\SimpleState;

/**
 * Class PropertyTest
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(Property::class)]
final class PropertyTest extends TestCase
{
    public function testConstructorWithSingleState(): void
    {
        $attr = new Property(SimpleState::class);

        $this->assertInstanceOf(Property::class, $attr);
    }

    public function testConstructorWithMultipleStates(): void
    {
        $attr = new Property([SimpleState::class, SimpleState::class]);

        $this->assertInstanceOf(Property::class, $attr);
    }

    public function testConstructorWithSingleConstraint(): void
    {
        $attr = new Property(
            SimpleState::class,
            ['property1', IsEqual::class, 'expectedValue']
        );

        $this->assertInstanceOf(Property::class, $attr);
    }

    public function testConstructorWithMultipleConstraints(): void
    {
        $attr = new Property(
            SimpleState::class,
            ['property1', IsEqual::class, 'expectedValue'],
            ['property2', IsNotNull::class]
        );

        $this->assertInstanceOf(Property::class, $attr);
    }

    public function testConstructorRejectsInvalidStateClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Property('NonExistentClass');
    }

    public function testConstructorRejectsNonStateInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Property(\stdClass::class);
    }

    public function testConstructorRejectsNonStringStateInArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Property([SimpleState::class, 123]);
    }

    public function testConstructorRejectsNonStringPropertyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each first `with` argument must be a property name');

        new Property(SimpleState::class, [123, IsEqual::class, 'value']);
    }

    public function testConstructorRejectsInvalidConstraintClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each second `with` argument must be a constraint class name');

        new Property(SimpleState::class, ['property1', 'InvalidClass']);
    }

    public function testConstructorRejectsNonConstraintClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each second `with` argument must be a constraint class name');

        new Property(SimpleState::class, ['property1', \stdClass::class]);
    }

    public function testGetAssertion(): void
    {
        $attr = new Property(SimpleState::class);

        $proxy = new MockProxy(null);
        $assertion = $attr->getAssertion($proxy);

        $this->assertInstanceOf(PropertyAssertion::class, $assertion);
    }

    public function testGetAssertionWithConstraints(): void
    {
        $attr = new Property(
            SimpleState::class,
            ['property1', IsEqual::class, 'expectedValue'],
            ['property2', IsNotNull::class]
        );

        $proxy = new MockProxy(null);
        $assertion = $attr->getAssertion($proxy);

        $this->assertInstanceOf(PropertyAssertion::class, $assertion);
        $this->assertEquals(
            new PropertyAssertion(SimpleState::class)
                ->with('property1', new IsEqual('expectedValue'))
                ->with('property2', new IsNotNull()),
            $assertion,
        );
    }
}
