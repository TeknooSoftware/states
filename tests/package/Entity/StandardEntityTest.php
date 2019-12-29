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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\UniversalPackage\States\Entity;

use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\Tests\UniversalPackage\States\Support;
use Teknoo\Tests\States\Proxy\StandardTest;

/**
 * Class StandardEntityTest.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\UniversalPackage\States\Entity\AbstractStandardEntity
 * @covers \Teknoo\UniversalPackage\States\Entity\StandardTrait
 */
class StandardEntityTest extends StandardTest
{
    /**
     * Build a proxy object, into $this->_proxy to test it.
     *
     * @return ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new Support\StandardEntity();

        return $this->proxy;
    }

    /**
     * Test if the class initialize its vars from the trait constructor.
     */
    public function testPostLoadDoctrine()
    {
        $proxyReflectionClass = new \ReflectionClass(Support\StandardEntity::class);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        self::assertInstanceOf(ProxyInterface::class, $proxy->postLoadDoctrine());

        return;
    }
}
