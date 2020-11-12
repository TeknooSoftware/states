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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Doctrine\Entity;

use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\Tests\States\Proxy\StandardTest;

/**
 * Class StandardEntityTest.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\States\Doctrine\Entity\AbstractStandardEntity
 * @covers \Teknoo\States\Doctrine\Entity\StandardTrait
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
        $this->proxy = new StandardEntity();

        return $this->proxy;
    }

    /**
     * Test if the class initialize its vars from the trait constructor.
     */
    public function testPostLoadDoctrine()
    {
        $proxyReflectionClass = new \ReflectionClass(StandardEntity::class);
        $proxy = $proxyReflectionClass->newInstanceWithoutConstructor();
        self::assertInstanceOf(ProxyInterface::class, $proxy->postLoadDoctrine());

        return;
    }
}
