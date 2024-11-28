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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Proxy;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Test behavior when passed states to proxy are invalid
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(Proxy\ProxyTrait::class)]
class BadStatesSupportTest extends TestCase
{
    public function testWithStateClassNotImplementStateInterface(): void
    {
        $this->expectException(Proxy\Exception\StateNotFound::class);
        $proxy = new Support\BadStateDefinedInProxyTest();
    }
}
