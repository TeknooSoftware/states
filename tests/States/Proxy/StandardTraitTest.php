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

use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Class StandardTraitTest
 * Implementation of AbstractProxyTests to test the trait Proxy\StandardTrait.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richard@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\States\Proxy\ProxyTrait
 * @covers \Teknoo\States\Proxy\ArrayAccessTrait
 * @covers \Teknoo\States\Proxy\IteratorTrait
 * @covers \Teknoo\States\Proxy\MagicCallTrait
 * @covers \Teknoo\States\Proxy\SerializableTrait
 */
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
