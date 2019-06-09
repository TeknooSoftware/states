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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Functional;

use Teknoo\Tests\Support\AutomatedAcme\AutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Class AutomatedTest.
 *
 * @covers \Teknoo\States\Automated\AutomatedTrait
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class AutomatedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return AutomatedAcme
     */
    public function buildInstance()
    {
        return new AutomatedAcme();
    }

    public function testUpdateStates()
    {
        $instance = $this->buildInstance();
        self::assertEquals([], $instance->listEnabledStates());

        $instance->setFoo('bar');
        self::assertEquals([], $instance->listEnabledStates());
        $instance->updateStates();
        self::assertEquals([State1::class], $instance->listEnabledStates());

        $instance->setFoo1('bar1')->setFoo2(123);
        self::assertEquals([State1::class], $instance->listEnabledStates());
        $instance->updateStates();
        self::assertEquals([State1::class], $instance->listEnabledStates());

        $instance->setFoo1('bar1')->setFoo2(null);
        self::assertEquals([State1::class], $instance->listEnabledStates());
        $instance->updateStates();
        self::assertEquals([State1::class, State2::class], $instance->listEnabledStates());

        $instance->setFoo('');
        self::assertEquals([State1::class, State2::class], $instance->listEnabledStates());
        $instance->updateStates();
        self::assertEquals([State2::class], $instance->listEnabledStates());

        $instance->setFoo1('');
        self::assertEquals([State2::class], $instance->listEnabledStates());
        $instance->updateStates();
        self::assertEquals([], $instance->listEnabledStates());
    }
}
