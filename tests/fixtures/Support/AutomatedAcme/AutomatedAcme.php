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
use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Class AutomatedAcme.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[StateClass(State1::class)]
#[StateClass(State2::class)]
#[Assertion\Property(State1::class, ['foo', Property\IsEqual::class, 'bar'])]
#[Assertion\Property(
    State2::class,
    ['foo1', Property\IsEqual::class, 'bar1'],
    ['foo2', Property\IsNull::class],
)]
class AutomatedAcme implements AutomatedInterface
{
    use ProxyTrait;
    use AutomatedTrait;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo1;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo2;

    /**
     * AutomatedAcme constructor.
     */
    public function __construct()
    {
        $this->initializeStateProxy();
    }

    public function setFoo(mixed $foo): static
    {
        $this->foo = $foo;

        return $this;
    }

    public function setFoo1(mixed $foo1): static
    {
        $this->foo1 = $foo1;

        return $this;
    }

    public function setFoo2(mixed $foo2): static
    {
        $this->foo2 = $foo2;

        return $this;
    }

    /**
     * Return the list of enabled states. Present only for debug and tests
     */
    public function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && \is_array($this->activesStates)) {
            return \array_keys($this->activesStates);
        }

        return [];
    }
}
