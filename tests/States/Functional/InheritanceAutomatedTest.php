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

namespace Teknoo\Tests\States\Functional;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\States\Functional\AutomatedTest;
use Teknoo\Tests\Support\AutomatedAcme\AutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\DaughterAutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\DaughterNonAutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Class AutomatedTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversTrait(AutomatedTrait::class)]
class InheritanceAutomatedTest extends AutomatedTest
{
    public function buildInstance()
    {
        return new DaughterAutomatedAcme();
    }

    public function testUpdateStatesWithDisabledInheritance(): void
    {
        $instance = new DaughterNonAutomatedAcme();
        $this->assertSame([], $instance->listEnabledStates());

        $instance->setFoo('bar');
        $this->assertSame([], $instance->listEnabledStates());
        $instance->updateStates();
        $this->assertSame([], $instance->listEnabledStates());
    }
}
