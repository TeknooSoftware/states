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

namespace Teknoo\Tests\States\Doctrine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use Teknoo\States\Doctrine\AbstractStandardObject;
use Teknoo\States\Doctrine\StandardTrait;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\Tests\States\Proxy\StandardTest;

/**
 * Class StandardObjectTest.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(AbstractStandardObject::class)]
#[CoversTrait(StandardTrait::class)]
class StandardObjectTest extends StandardTest
{
    /**
     * Build a proxy object, into $this->_proxy to test it.
     *
     * @return ProxyInterface
     */
    #[\Override]
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
        $this->assertInstanceOf(ProxyInterface::class, $proxy->postLoadDoctrine());
    }

    public function testCallNonExistantMethodWithUninitializedNativeLazyObject(): void
    {
        $reflectionClass = new \ReflectionClass(StandardObject::class);

        $initialized = false;
        $proxy = $reflectionClass->newLazyGhost(function (StandardObject $object) use (&$initialized): void {
            $initialized = true;
            $object->__construct();
        });

        $this->assertTrue($reflectionClass->isUninitializedLazyObject($proxy));
        $this->assertFalse($initialized);

        try {
            $proxy->nonExistentMethod();
        } catch (MethodNotImplemented) {
            // Expected exception
        }

        $this->assertTrue($initialized);
        $this->assertFalse($reflectionClass->isUninitializedLazyObject($proxy));
    }

    public function testCallNonExistantMethodWithInitializedNativeLazyObject(): void
    {
        $reflectionClass = new \ReflectionClass(StandardObject::class);

        $initCount = 0;
        $proxy = $reflectionClass->newLazyGhost(function (StandardObject $object) use (&$initCount): void {
            $initCount++;
            $object->__construct();
        });

        $reflectionClass->initializeLazyObject($proxy);
        $this->assertFalse($reflectionClass->isUninitializedLazyObject($proxy));
        $this->assertEquals(1, $initCount);

        try {
            $proxy->nonExistentMethod();
        } catch (MethodNotImplemented) {
            // Expected exception
        }

        $this->assertEquals(1, $initCount);
    }

    public function testCallWithNonLazyObject(): void
    {
        $proxy = new StandardObject();

        $result = $proxy->publicMethodToCall();
        $this->assertEquals('fooBar', $result);
    }
}
