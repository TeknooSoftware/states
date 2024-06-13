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
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Proxy;

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\Exception;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\States\State\AbstractState;
use Teknoo\Tests\Support\MockState1;

/**
 * Class PrivateTestTrait
 * To perform some trait about behavior of magic call for private attributes and method.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin AbstractState
 */
trait PrivateTestTrait
{
    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testPHPExceptionWhenAChildCanAccessToPrivatePropertyOfMother(): void
    {
        //PHPUnit intercepts the Notice exception ;)
        $fail = false;
        if (!class_exists(Warning::class)) {
            $previous = set_error_handler(
                function () use (&$fail) {
                    $fail = true;
                },
                E_WARNING
            );
        } else {
            $this->expectException(Exception::class);
        }
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->getChildrenPriProperty();

        if (null !== $previous) {
            self::assertTrue($fail);
            \restore_error_handler();
        }
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testIssetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        self::assertFalse($this->proxy->issetChildrenPriProperty());
        self::assertFalse($this->proxy->issetChildrenMissingPriProperty());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testSetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->setChildrenPriProperty('value2');
        self::assertEquals('value2', $this->proxy->getChildrenPriProperty());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testUnsetPHPBehaviorWhenAChildCanAccessToPrivatePropertyOfMother(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        self::assertEmpty($this->proxy->unsetChildrenPriProperty());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testGetIssetSetUnsetPrivateViaMethodChildren(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
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
    public function testCallPrivateFromState(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        self::assertEquals('fooBar', $this->proxy->callPriMethod());
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testCallPrivateChildrenFromOutside(): void
    {
        $this->expectException(MethodNotImplemented::class);
        $this->proxy->parentPrivateMethodToCall();
    }

    /**
     * Test behavior of magic method during a state's method calling (scope is not initialized).
     */
    public function testCallPrivateChildrenFromState(): void
    {
        $this->initializeStateProxy(MockState1::class, true);
        $this->proxy->callChildrenPriMethod();
        self::assertTrue($this->state1->methodWasCalled());
    }
}
