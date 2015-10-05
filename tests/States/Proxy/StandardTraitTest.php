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
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\Proxy;

use UniAlteri\States\Proxy;
use UniAlteri\Tests\Support;

/**
 * Class StandardTraitTest
 * Implementation of AbstractProxyTest to test the proxy Proxy\Standard.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @covers UniAlteri\States\Proxy\ProxyTrait
 * @covers UniAlteri\States\Proxy\ArrayAccessTrait
 * @covers UniAlteri\States\Proxy\IteratorTrait
 * @covers UniAlteri\States\Proxy\MagicCallTrait
 * @covers UniAlteri\States\Proxy\SerializableTrait
 */
class StandardTraitTest extends AbstractProxyTest
{
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

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized)
     * @expectedException \ErrorException
     */
    public function testGetPrivateGetMethodChildren()
    {
        $this->initializeProxy('state1', true);
        $this->proxy->getChildrenPriProperty();
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized)
     */
    public function testIssetPrivateIssetMethodChildren()
    {
        $this->initializeProxy('state1', true);
        $this->assertFalse($this->proxy->issetChildrenPriProperty());
        $this->assertFalse($this->proxy->issetChildrenMissingPriProperty());
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized)
     * @expectedException \ErrorException
     */
    public function testSetUnsetPrivateMethodChildren()
    {
        $this->initializeProxy('state1', true);
        $this->proxy->setChildrenPriProperty('value2');
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized)
     * @expectedException \ErrorException
     */
    public function testUnsetPrivateMethodChildren()
    {
        $this->initializeProxy('state1', true);
        $this->proxy->unsetChildrenPriProperty();
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized)
     */
    public function testGetIssetSetUnsetPrivateViaMethod()
    {
        $this->initializeProxy('state1', true);
        $this->assertEquals('value1', $this->proxy->getPriProperty());
        $this->assertTrue($this->proxy->issetPriProperty());
        $this->assertFalse($this->proxy->issetMissingPriProperty());
        $this->proxy->setPriProperty('value2');
        $this->assertEquals('value2', $this->proxy->getPriProperty());
        $this->proxy->unsetPriProperty();
        $this->assertFalse($this->proxy->issetPriProperty());
    }
}
