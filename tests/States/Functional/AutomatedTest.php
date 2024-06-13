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

namespace Teknoo\Tests\States\Functional;

use PHPUnit\Framework\Attributes\CoversClass;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\Tests\Support\AutomatedAcme\AutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Class AutomatedTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(AutomatedTrait::class)]
class AutomatedTest extends \PHPUnit\Framework\TestCase
{
    public function buildInstance(): \Teknoo\Tests\Support\AutomatedAcme\AutomatedAcme
    {
        return new AutomatedAcme();
    }

    public function testUpdateStates(): void
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
