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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\Tests\States\Proxy;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support;

/**
 * Class StandardTraitTest
 * Implementation of AbstractProxyTest to test the trait Proxy\StandardTrait.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @covers Teknoo\States\Proxy\ProxyTrait
 * @covers Teknoo\States\Proxy\ArrayAccessTrait
 * @covers Teknoo\States\Proxy\IteratorTrait
 * @covers Teknoo\States\Proxy\MagicCallTrait
 * @covers Teknoo\States\Proxy\SerializableTrait
 */
class StandardTraitTest extends AbstractProxyTest
{
    use PrivateTestTrait;

    /**
     * Build a proxy object, into $this->proxy to test it.
     *
     * @return Proxy\ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new Support\StandardTraitProxy();

        return $this->proxy;
    }
}