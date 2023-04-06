<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Proxy;

use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Test behavior when passed states to proxy are invalid
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\States\Proxy\ProxyTrait
 */
class BadStatesSupportTest extends TestCase
{
    public function testWithStateClassNotImplementStateInterface(): void
    {
        $this->expectException(Proxy\Exception\StateNotFound::class);
        $proxy = new Support\BadStateDefinedInProxyTest();
    }
}
