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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CallbackTest extends AbstractAssertionTest
{
    /**
     * @return Callback|AssertionInterface
     */
    public function buildInstance(): callable|AssertionInterface
    {
        return new Callback(['state1', 'state2']);
    }

    public function testCallClosure()
    {
        self::assertInstanceOf(
            Callback::class,
            $this->buildInstance()->call(function () {
            })
        );
    }

    public function testCallCalback()
    {
        self::assertInstanceOf(
            Callback::class,
            $this->buildInstance()->call('time')
        );
    }

    public function testBadCallable()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->call('badFunctionName');
    }

    public function testExceptionWhenCheckAssertionWithNoCallbackDefined()
    {
        $this->expectException(\RuntimeException::class);
        $this->buildInstance()->check($this->createMock(AutomatedInterface::class));
    }

    public function testCheckCallbackIsValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::exactly(2))
            ->method('enableState')
            ->withConsecutive(['state1'], ['state2'])
            ->willReturnSelf();

        $assertion->call(function ($proxy, $callback) {
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

    public function testCheckCallbackIsNotValid()
    {
        $assertion = $this->buildInstance();
        $proxy = $this->createMock(AutomatedInterface::class);
        $proxy->expects(self::never())
            ->method('enableState');

        $assertion->call(function ($proxy, $callback) {
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
