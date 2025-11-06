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

namespace Teknoo\Tests\States\Attributes;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Attributes\StateClass;
use Teknoo\Tests\Support\States\SimpleState;

/**
 * Class StateClassTest
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(StateClass::class)]
final class StateClassTest extends TestCase
{
    public function testAcceptsSingleClassString(): void
    {
        $attr = new StateClass(SimpleState::class);
        $this->assertSame([SimpleState::class], $attr->getClassNames());
    }

    public function testAcceptsListOfClassStrings(): void
    {
        $classes = [SimpleState::class, SimpleState::class];
        $attr = new StateClass($classes);
        $this->assertSame($classes, $attr->getClassNames());
    }

    public function testRejectsEmptyList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StateClass([]);
    }

    public function testRejectsNonExistingClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StateClass('Foo\\Bar\\BazNonExisting');
    }

    public function testRejectsClassNotImplementingStateInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StateClass(\stdClass::class);
    }
}
