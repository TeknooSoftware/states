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

use Teknoo\States\Automated\Assertion\Callback;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class CallbackTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 * @covers \Teknoo\States\Automated\Assertion\Callback
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CallbackTest extends AbstractAssertionTest
{
    /**
     * @return Callback
     */
    public function buildInstance()
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

    /**
     * @expectedException \TypeError
     */
    public function testBadCallable()
    {
        $this->buildInstance()->call('badFunctionName');
    }
}
