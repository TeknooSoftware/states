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

namespace Teknoo\Tests\States\Automated\Assertion\Property;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Assertion\Property\IsEqual;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractConstraintTests extends \PHPUnit\Framework\TestCase
{
    abstract public function buildInstance(): ConstraintInterface;

    public function testInConstraintSetInstanceNotImplement(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->inConstraintSet(new \stdClass());
    }

    public function testInConstraintSet(): void
    {
        $instance = $this->buildInstance();
        $instanceWithSet = $instance->inConstraintSet($this->createMock(ConstraintsSetInterface::class));

        $this->assertInstanceOf(ConstraintInterface::class, $instance);

        $this->assertInstanceOf(ConstraintInterface::class, $instanceWithSet);

        $this->assertNotSame($instance, $instanceWithSet);
    }
}
