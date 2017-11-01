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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
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
     * @param array $assertions
     * @return AutomatedInterface
     */
    public function buildProxy(array $assertions): AutomatedInterface
    {
        return new class($assertions) implements AutomatedInterface
        {
            use AutomatedTrait;
            use ProxyTrait;

            private $assertions;

            public function __construct(array $assertions)
            {
                $this->assertions = $assertions;
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

    /**
     * @expectedException \RuntimeException
     */
    public function testBadAssertions()
    {
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

    /**
     * @expectedException \TypeError
     */
    public function testExceptionOnCheckPropertyWithBadProperty()
    {
        $this->buildProxy([])
            ->checkProperty(new \stdClass(), $this->createMock(ConstraintsSetInterface::class));
    }

    /**
     * @expectedException \TypeError
     */
    public function testExceptionOnCheckPropertyWithBadConstraintSet()
    {
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