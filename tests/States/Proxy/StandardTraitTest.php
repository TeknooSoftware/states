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
use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Class StandardTraitTest
 * Implementation of AbstractProxyTests to test the trait Proxy\StandardTrait.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(Proxy\ProxyTrait::class)]
#[CoversClass(Proxy\ArrayAccessTrait::class)]
#[CoversClass(Proxy\IteratorTrait::class)]
#[CoversClass(Proxy\MagicCallTrait::class)]
#[CoversClass(Proxy\SerializableTrait::class)]
class StandardTraitTest extends AbstractProxyTests
{
    use PrivateTestTrait;

    /**
     * Build a proxy object, into $this->proxy to test it.
     */
    protected function buildProxy(): \Proxy\ProxyInterface|\Teknoo\Tests\Support\StandardTraitProxy
    {
        $this->proxy = new Support\StandardTraitProxy();

        return $this->proxy;
    }

    public function testExceptionWhenStateIsNotCorrectlyInitializedWithItsAssociatedClassName(): void
    {
        $this->expectException(\RuntimeException::class);
        $proxy = $this->buildProxy();
        $proxy->registerStateWithoutOriginal('badState', new Support\MockState1(false, Support\StandardTraitProxy::class));

        $this->proxy->test();
    }
}
