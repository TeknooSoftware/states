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

use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\Support\Extendable\Daughter\StaticCallerDaughter;
use Teknoo\Tests\Support\Extendable\Mother\Mother;
use Teknoo\Tests\Support\Extendable\Mother\States\StateCloner;
use Teknoo\Tests\Support\Extendable\Mother\States\StateTwo;

/**
 * Non regression tests about the visibility scope granted to static callers : it must only depend on the caller,
 * never on calls previously performed on the stated class instance.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class StaticCallerVisibilityTest extends TestCase
{
    private function buildDaughter(): StaticCallerDaughter
    {
        $daughter = new StaticCallerDaughter();
        $daughter->enableState(StateTwo::class);

        return $daughter;
    }

    private function buildMother(): Mother
    {
        $mother = new Mother();
        $mother->enableState(StateTwo::class);

        return $mother;
    }

    public function testChildStaticMethodCanNotCallParentPrivateOnFreshInstance(): void
    {
        $daughter = $this->buildDaughter();

        $this->expectException(MethodNotImplemented::class);
        StaticCallerDaughter::stealMotherPrivate($daughter);
    }

    public function testChildStaticMethodCanNotCallParentPrivateAfterALegitimateCall(): void
    {
        $daughter = $this->buildDaughter();

        //Legitimate call : a public method of the mother's state calls a private method of this same state
        $this->assertSame(1578, $daughter->methodRecallPrivate());

        $this->expectException(MethodNotImplemented::class);
        StaticCallerDaughter::stealMotherPrivate($daughter);
    }

    /**
     * An instance cloned during the execution of a state's method (like immutable objects do) must not keep the
     * stated class owning this method as caller : else, all next callers of the clone are considered as this stated
     * class and can call its private methods.
     */
    public function testCloneCreatedInAStateMethodDoesNotKeepItsCaller(): void
    {
        $daughter = $this->buildDaughter();
        //State owned by the mother class, like the state StateTwo, owning the private method
        $daughter->registerState(StateCloner::class, new StateCloner(true, Mother::class), Mother::class);
        $daughter->enableState(StateCloner::class);

        $clone = $daughter->cloneMe();
        $this->assertInstanceOf(StaticCallerDaughter::class, $clone);
        $this->assertNotSame($daughter, $clone);

        //Legitimate call : a public method of the mother's state calls a private method of this same state
        $this->assertSame(1578, $clone->methodRecallPrivate());

        $this->expectException(MethodNotImplemented::class);
        StaticCallerDaughter::stealMotherPrivate($clone);
    }

    public function testChildStaticMethodCanCallParentProtectedOnFreshParentInstance(): void
    {
        $this->assertSame(456, StaticCallerDaughter::callMotherProtected($this->buildMother()));
    }

    public function testChildStaticMethodCanCallParentProtectedAfterALegitimateCall(): void
    {
        $mother = $this->buildMother();

        $this->assertSame(1578, $mother->methodRecallPrivate());
        $this->assertSame(456, StaticCallerDaughter::callMotherProtected($mother));
    }
}
