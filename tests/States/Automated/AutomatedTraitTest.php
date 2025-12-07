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

namespace Teknoo\Tests\States\Automated;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\Tests\Support\States\SimpleState;

/**
 * Class AbstractAutomatedTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversTrait(AutomatedTrait::class)]
class AutomatedTraitTest extends TestCase
{
    public function buildProxy(mixed $assertions): AutomatedInterface
    {
        $object = new #[StateClass([SimpleState::class])] class ($assertions) extends stdClass implements AutomatedInterface {
            use AutomatedTrait;
            use ProxyTrait;

            public function __construct(private readonly mixed $assertions)
            {
                $this->initializeStateProxy();
            }

            protected function listAssertions(): mixed
            {
                return $this->assertions;
            }
        };

        return $object;
    }

    public function testBadAssertionInstance(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->buildProxy([new stdClass()])->updateStates();
    }

    public function testBadAssertionsList(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->buildProxy(new stdClass())->updateStates();
    }

    public function testUpdateStatesCallAssertions(): void
    {
        $assertion1 = $this->createMock(AssertionInterface::class);
        $assertion1->expects($this->once())->method('check');
        $assertion2 = $this->createMock(AssertionInterface::class);
        $assertion2->expects($this->once())->method('check');

        $this->assertInstanceOf(
            AutomatedInterface::class,
            $this->buildProxy([$assertion1, $assertion2])->updateStates(),
        );
    }

    public function testExceptionOnCheckPropertyWithBadProperty(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy([])
            ->checkProperty(new stdClass(), $this->createStub(ConstraintsSetInterface::class));
    }

    public function testExceptionOnCheckPropertyWithBadConstraintSet(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy([])
            ->checkProperty('name', new stdClass());
    }

    public function testCheckPropertyWithUnsetProperty(): void
    {
        $set = $this->createMock(ConstraintsSetInterface::class);
        $set->expects($this->once())
            ->method('check')
            ->with(null)
            ->willReturnSelf();

        $this->assertInstanceOf(AutomatedInterface::class, $this->buildProxy([])->checkProperty('prop1', $set));
    }

    public function testCheckPropertyWithSetProperty(): void
    {
        $set = $this->createMock(ConstraintsSetInterface::class);
        $set->expects($this->once())
            ->method('check')
            ->with('fooBar')
            ->willReturnSelf();

        $proxy = $this->buildProxy([]);
        $proxy->prop1 = 'fooBar';

        $this->assertInstanceOf(AutomatedInterface::class, $proxy->checkProperty('prop1', $set));
    }
}
