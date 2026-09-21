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

namespace Teknoo\Tests\Support\Person;

use DateTimeInterface;
use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Proxy\Standard;
use Teknoo\Tests\Support\Person\States\English;
use Teknoo\Tests\Support\Person\States\French;

/**
 * Stated class written like in the quick example of the README : the proxy extends the Standard class and its
 * states extend the AbstractState class. Builders return closures or arrow functions, using private properties and
 * private methods of the proxy.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[StateClass(English::class)]
#[StateClass(French::class)]
class Person extends Standard
{
    public function __construct(
        private string $name,
        private string $country,
    ) {
        parent::__construct();

        $this->enableState(
            match ($this->country) {
                'fr' => French::class,
                default => English::class,
            }
        );
    }

    private function getName(): string
    {
        return $this->name;
    }

    public function introduce(DateTimeInterface $now): string
    {
        return $this->sayHello() . ' (' . $this->displayDate($now) . ')';
    }
}
