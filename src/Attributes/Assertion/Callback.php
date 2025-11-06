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

namespace Teknoo\States\Attributes\Assertion;

use Attribute;
use InvalidArgumentException;
use Teknoo\States\Attributes\AssertionInterface;
use Teknoo\States\Automated\Assertion\AssertionInterface as AutomatedAssertionInterface;
use Teknoo\States\Automated\Assertion\Callback as AssertionCallback;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_values;
use function class_exists;
use function is_a;
use function is_callable;
use function is_string;

/**
 * Attribute to declare one or many state class names for a proxy class.
 *
 * Usage examples:
 *   #[StateClass(FooState::class)]
 *   #[StateClass([FooState::class, BarState::class])]
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Callback implements AssertionInterface
{
    /**
     * @var list<class-string<StateInterface>>
     */
    private array $states = [];

    /**
     * @var callable|string
     */
    private $callback;

    /**
     * @param string|string[] $states
     */
    public function __construct(
        string|array $states,
        string|callable $callback,
    ) {
        if (is_string($states)) {
            $states = [$states];
        }

        foreach ($states as $name) {
            if (
                !is_string($name)
                || !class_exists(class: $name, autoload: true)
                || !is_a($name, StateInterface::class, true)
            ) {
                throw new InvalidArgumentException(
                    'Each state class must be a non-empty class-string implementing ' . StateInterface::class
                );
            }

            $this->states[] = $name;
        }

        $this->callback = $callback;
    }

    public function getAssertion(ProxyInterface $proxy): AutomatedAssertionInterface
    {
        $callback = $this->callback;
        if (!is_callable($callback)) {
            $callback = [$proxy, $callback];
        }

        /** @var callable $callback */
        return new AssertionCallback($this->states)->call($callback);
    }
}
