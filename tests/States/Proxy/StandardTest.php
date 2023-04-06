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
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
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
 * Class StandardTest
 * Implementation of AbstractProxyTests to test the proxy Proxy\Standard.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\States\Proxy\ProxyTrait
 * @covers \Teknoo\States\Proxy\Standard
 * @covers \Teknoo\States\Proxy\ArrayAccessTrait
 * @covers \Teknoo\States\Proxy\IteratorTrait
 * @covers \Teknoo\States\Proxy\MagicCallTrait
 * @covers \Teknoo\States\Proxy\SerializableTrait
 */
class StandardTest extends AbstractProxyTests
{
    /**
     * Build a proxy object, into $this->proxy to test it.
     *
     * @return Proxy\ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new Support\StandardProxy();

        return $this->proxy;
    }
}
