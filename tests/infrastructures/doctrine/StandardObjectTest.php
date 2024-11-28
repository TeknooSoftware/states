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
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Doctrine;

use PHPUnit\Framework\Attributes\CoversClass;
use Teknoo\States\Doctrine\AbstractStandardObject;
use Teknoo\States\Doctrine\StandardTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\Tests\States\Proxy\StandardTest;

/**
 * Class StandardObjectTest.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(AbstractStandardObject::class)]
#[CoversClass(StandardTrait::class)]
class StandardObjectTest extends StandardTest
{
    /**
     * Build a proxy object, into $this->_proxy to test it.
     *
     * @return ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new StandardObject();

        return $this->proxy;
    }

    /**
     * Test if the class initialize its vars from the trait constructor.
     */
    public function testPostLoadDoctrine(): void
    {
        $proxyReflectionClass = new \ReflectionClass(StandardObject::class);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        self::assertInstanceOf(ProxyInterface::class, $proxy->postLoadDoctrine());

        return;
    }
}
