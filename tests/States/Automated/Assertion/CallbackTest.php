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

use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Callback;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class CallbackTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 * @covers \Teknoo\States\Automated\Assertion\Callback
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class CallbackTest extends AbstractAssertionTests
{
    public function buildInstance(): callable|AssertionInterface
    {
        return new Callback(['state1', 'state2']);
    }

    public function testCallClosure(): void
    {
        self::assertInstanceOf(
            Callback::class,
            $this->buildInstance()->call(function (): void {
            })
        );
    }

    public function testCallCalback(): void
    {
        self::assertInstanceOf(
            Callback::class,
            $this->buildInstance()->call('time')
        );
    }

    public function testBadCallable(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->call('badFunctionName');
    }

    public function testExceptionWhenCheckAssertionWithNoCallbackDefined(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->buildInstance()->check($this->createMock(AutomatedInterface::class));
    }

    public function testCheckCallbackIsValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::exactly(2))
            ->method('enableState')
            ->with($this->callback(
                fn ($value) => match ($value) {
                    'state1' => true,
                    'state2' => true,
                    default => false,
                }
            ))
            ->willReturnSelf();

        $assertion->call(function ($proxy, $callback): void {
            self::assertInstanceOf(AutomatedInterface::class, $proxy);
            self::assertInstanceOf(Callback::class, $callback);

            self::assertInstanceOf(Callback::class, $callback->isValid());
        });

        $assertion2 = $assertion->check($proxy);

        self::assertInstanceOf(
            Callback::class,
            $assertion2
        );

        self::assertNotSame(
            $assertion,
            $assertion2
        );
    }

    public function testCheckCallbackIsNotValid(): void
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::never())
            ->method('enableState');

        $assertion->call(function ($proxy, $callback): void {
            self::assertInstanceOf(AutomatedInterface::class, $proxy);
            self::assertInstanceOf(Callback::class, $callback);
        });

        $assertion2 = $assertion->check($proxy);

        self::assertInstanceOf(
            Callback::class,
            $assertion2
        );

        self::assertNotSame(
            $assertion,
            $assertion2
        );
    }
}
