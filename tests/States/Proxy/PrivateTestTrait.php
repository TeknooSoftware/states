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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\States\Proxy;

use Teknoo\States\State\AbstractState;
use Teknoo\Tests\Support\MockState1;

/**
 * Class PrivateTestTrait
 * To perform some trait about behavior of magic call for private attributes and method.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin AbstractState
 */
trait PrivateTestTrait
{
    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     * @expectedException \PHPUnit\Framework\Exception
     * @expectedException \Throwable
     */
    public function testPHPExceptionWhenAChildCanAccessToPrivatePropertyOfMother()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->getChildrenPriProperty();
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     */
    public function testIssetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother()
    {
        $this->initializeProxy(MockState1::class, true);
        self::assertFalse($this->proxy->issetChildrenPriProperty());
        self::assertFalse($this->proxy->issetChildrenMissingPriProperty());
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     */
    public function testSetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->setChildrenPriProperty('value2');
        self::assertEquals('value2', $this->proxy->getChildrenPriProperty());
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     */
    public function testUnsetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother()
    {
        $this->initializeProxy(MockState1::class, true);
        self::assertEmpty($this->proxy->unsetChildrenPriProperty());
    }

    /**
     * Test behavior of magic method during a state's methode calling (scope is not initialized).
     */
    public function testGetIssetSetUnsetPrivateViaMethodChildren()
    {
        $this->initializeProxy(MockState1::class, true);
        self::assertEquals('value1', $this->proxy->getPriProperty());
        self::assertTrue($this->proxy->issetPriProperty());
        self::assertFalse($this->proxy->issetMissingPriProperty());
        $this->proxy->setPriProperty('value2');
        self::assertEquals('value2', $this->proxy->getPriProperty());
        $this->proxy->unsetPriProperty();
        self::assertFalse($this->proxy->issetPriProperty());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testCallPrivateFromState()
    {
        $this->initializeProxy(MockState1::class, true);
        self::assertEquals('fooBar', $this->proxy->callPriMethod());
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
     */
    public function testCallPrivateChildrenFromState()
    {
        $this->initializeProxy(MockState1::class, true);
        $this->proxy->callChildrenPriMethod();
        self::assertTrue($this->state1->methodWasCalled());
    }
}
