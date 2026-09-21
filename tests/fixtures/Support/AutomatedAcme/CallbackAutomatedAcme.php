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

namespace Teknoo\Tests\Support\AutomatedAcme;

use Teknoo\States\Attributes\Assertion;
use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Automated stated class whose assertions are callbacks referencing, in attributes, methods of this class by
 * theirs names : these methods check values of the instance owning them.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[StateClass(State1::class)]
#[StateClass(State2::class)]
#[Assertion\Callback(State1::class, 'assertFooIsBar')]
#[Assertion\Callback(State2::class, 'assertFooIsNotBar')]
class CallbackAutomatedAcme implements AutomatedInterface
{
    use ProxyTrait;
    use AutomatedTrait;

    public function __construct(
        private mixed $foo = null,
    ) {
        $this->initializeStateProxy();
    }

    public function setFoo(mixed $foo): static
    {
        $this->foo = $foo;

        return $this;
    }

    public function assertFooIsBar(ProxyInterface $proxy, AssertionInterface $assertion): void
    {
        if ('bar' === $this->foo) {
            $assertion->isValid();
        }
    }

    public function assertFooIsNotBar(ProxyInterface $proxy, AssertionInterface $assertion): void
    {
        if ('bar' !== $this->foo) {
            $assertion->isValid();
        }
    }

    /**
     * @return array<string>
     */
    public function listEnabledStates(): array
    {
        return \array_keys($this->activesStates);
    }
}
