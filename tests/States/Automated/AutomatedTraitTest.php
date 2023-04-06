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
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Automated;

use stdClass;
use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * Class AbstractAutomatedTest.
 *
 * @covers \Teknoo\States\Automated\AutomatedTrait
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\Automated\AutomatedTrait
 */
class AutomatedTraitTest extends \PHPUnit\Framework\TestCase
{
    public function buildProxy(array $assertions): AutomatedInterface
    {
        return new class($assertions) extends stdClass implements AutomatedInterface {
            use AutomatedTrait;
            use ProxyTrait;

            public function __construct(private readonly array $assertions)
            {
            }

            protected static function statesListDeclaration(): array
            {
                return [];
            }

            protected function listAssertions(): array
            {
                return $this->assertions;
            }
        };
    }

    public function testBadAssertions(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->buildProxy([new stdClass()])->updateStates();
    }

    public function testUpdateStatesCallAssertions(): void
    {
        $assertion1 = $this->createMock(AssertionInterface::class);
        $assertion1->expects(self::once())->method('check');
        $assertion2 = $this->createMock(AssertionInterface::class);
        $assertion2->expects(self::once())->method('check');

        self::assertInstanceOf(
            AutomatedInterface::class,
            $this->buildProxy([$assertion1, $assertion2])->updateStates()
        );
    }

    public function testExceptionOnCheckPropertyWithBadProperty(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy([])
            ->checkProperty(new stdClass(), $this->createMock(ConstraintsSetInterface::class));
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
        $set->expects(self::once())
            ->method('check')
            ->with(null)
            ->willReturnSelf();

        self::assertInstanceOf(
            AutomatedInterface::class,
            $this->buildProxy([])->checkProperty('prop1', $set)
        );
    }

    public function testCheckPropertyWithSetProperty(): void
    {
        $set = $this->createMock(ConstraintsSetInterface::class);
        $set->expects(self::once())
            ->method('check')
            ->with('fooBar')
            ->willReturnSelf();

        $proxy = $this->buildProxy([]);
        $proxy->prop1 = 'fooBar';

        self::assertInstanceOf(
            AutomatedInterface::class,
            $proxy->checkProperty('prop1', $set)
        );
    }
}
