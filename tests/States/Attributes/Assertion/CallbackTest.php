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
use Teknoo\States\Attributes\Assertion\Callback;
use Teknoo\States\Automated\Assertion\Callback as AssertionCallback;
use Teknoo\States\Automated\Assertion\Callback as CallbackAssertion;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\Tests\Support\MockProxy;
use Teknoo\Tests\Support\States\SimpleState;

/**
 * Class CallbackTest
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(Callback::class)]
final class CallbackTest extends TestCase
{
    public static function toCall(): void
    {
    }

    public function testConstructorWithSingleStateAndCallable(): void
    {
        $attr = new Callback(SimpleState::class, self::toCall(...));

        $this->assertInstanceOf(Callback::class, $attr);
    }

    public function testConstructorWithMultipleStatesAndCallable(): void
    {
        $attr = new Callback([SimpleState::class, SimpleState::class], self::toCall(...));

        $this->assertInstanceOf(Callback::class, $attr);
    }

    public function testConstructorWithStringCallback(): void
    {
        $attr = new Callback(SimpleState::class, 'someMethod');

        $this->assertInstanceOf(Callback::class, $attr);
    }

    public function testConstructorRejectsInvalidStateClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Callback('NonExistentClass', self::toCall(...));
    }

    public function testConstructorRejectsNonStateInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Callback(\stdClass::class, self::toCall(...));
    }

    public function testConstructorRejectsNonStringStateInArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each state class must be a non-empty class-string implementing');

        new Callback([SimpleState::class, 123], self::toCall(...));
    }

    public function testGetAssertionWithCallableCallback(): void
    {
        $attr = new Callback(SimpleState::class, self::toCall(...));

        $assertion = $attr->getAssertion($this->createStub(ProxyInterface::class));

        $this->assertInstanceOf(AssertionCallback::class, $assertion);

        $this->assertEquals(
            new CallbackAssertion([SimpleState::class])->call(self::toCall(...)),
            $assertion,
        );
    }

    public function testGetAssertionWithMethodNameCallback(): void
    {
        $attr = new Callback(SimpleState::class, 'registerState');

        $assertion = $attr->getAssertion($proxy = $this->createStub(ProxyInterface::class));

        $this->assertInstanceOf(AssertionCallback::class, $assertion);

        $this->assertEquals(
            new CallbackAssertion([SimpleState::class])->call([$proxy, 'registerState']),
            $assertion,
        );
    }
}
