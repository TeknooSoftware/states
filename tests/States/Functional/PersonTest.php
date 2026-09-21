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

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\Support\Person\Person;
use Teknoo\Tests\Support\Person\States\English;
use Teknoo\Tests\Support\Person\States\French;

/**
 * Functional test of a stated class written like in the quick example of the README : proxy extending the Standard
 * class, states extending the AbstractState class, builders returning closures or arrow functions.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class PersonTest extends TestCase
{
    public function testBehaviorChangesWithTheEnabledState(): void
    {
        $now = new DateTimeImmutable('2016-07-01');

        $frenchMan = new Person('Roger', 'fr');
        $this->assertSame('Bonjour, Roger', $frenchMan->sayHello());
        $this->assertSame('Roger, 01 07 2016', $frenchMan->displayDate($now));
        $this->assertSame('Bonjour, Roger (Roger, 01 07 2016)', $frenchMan->introduce($now));

        $englishMan = new Person('Richard', 'en');
        $this->assertSame('Good morning, Richard', $englishMan->sayHello());
        $this->assertSame('Richard, 07 01, 2016', $englishMan->displayDate($now));

        $englishMan->switchState(French::class);
        $this->assertSame('Bonjour, Richard', $englishMan->sayHello());

        $called = false;
        $englishMan->isNotInState([English::class], function () use (&$called): void {
            $called = true;
        });
        $this->assertTrue($called);
    }

    /**
     * A private method of the proxy, used by states, is not visible from outside : PHP forwards the call to the
     * proxy's method `__call()`, which does not find it in enabled states.
     */
    public function testPrivateMethodOfTheProxyIsNotAvailableFromOutside(): void
    {
        $person = new Person('Richard', 'en');

        $this->expectException(MethodNotImplemented::class);
        $person->getName();
    }

    public function testNoMethodAvailableWithoutEnabledState(): void
    {
        $person = new Person('Richard', 'en');
        $person->disableAllStates();

        $this->expectException(MethodNotImplemented::class);
        $person->sayHello();
    }
}
