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
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\Proxy;

/**
 * Class PrivateTestTrait
 * To perform some trait about behavior of magic call for private attributes and method.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait PrivateTestTrait
{
    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     */
    public function testGetIssetSetUnsetPrivateViaMethodChildren()
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

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testCallPrivateFromState()
    {
        $this->initializeProxy('state1', true);
        $this->assertEquals('fooBar', $this->proxy->callPriMethod());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     *
     * @expectedException \Throwable
     */
    public function testCallPrivateChildrenFromOutside()
    {
        $this->proxy->parentPrivateMethodToCall();
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     *
     * @expectedException \Throwable
     */
    public function testCallPrivateChildrenFromState()
    {
        $this->initializeProxy('state1', true);
        $this->proxy->callChildrenPriMethod();
    }
}
