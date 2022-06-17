<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Automated;

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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\Automated\AutomatedTrait
 */
class AutomatedTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return AutomatedInterface
     */
    public function buildProxy(array $assertions): AutomatedInterface
    {
        return new class($assertions) implements AutomatedInterface {
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

    public function testBadAssertions()
    {
        $this->expectException(\RuntimeException::class);
        $this->buildProxy([new \stdClass()])->updateStates();
    }

    public function testUpdateStatesCallAssertions()
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

    public function testExceptionOnCheckPropertyWithBadProperty()
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy([])
            ->checkProperty(new \stdClass(), $this->createMock(ConstraintsSetInterface::class));
    }

    public function testExceptionOnCheckPropertyWithBadConstraintSet()
    {
        $this->expectException(\TypeError::class);
        $this->buildProxy([])
            ->checkProperty('name', new \stdClass());
    }

    public function testCheckPropertyWithUnsetProperty()
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

    public function testCheckPropertyWithSetProperty()
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
