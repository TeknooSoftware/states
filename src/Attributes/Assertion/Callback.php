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
use function method_exists;
use function str_contains;

/**
 * Attribute to declare, on an automated proxy class, an assertion delegated to a callback : listed states are enabled
 * by `updateStates()` when the callback calls the method `isValid()` of the assertion. The callback is the name of a
 * public method of the proxy (always preferred to a PHP function with the same name) or a callable. It is called with
 * the proxy and the assertion.
 *
 * Usage examples:
 *   #[Callback(FooState::class, 'aMethodOfTheProxy')]
 *   #[Callback([FooState::class, BarState::class], [Foo::class, 'aStaticMethod'])]
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
        if (
            !is_callable($callback)
            || (is_string($callback) && !str_contains($callback, '::') && method_exists($proxy, $callback))
        ) {
            //It is the name of a method of the proxy. A method of the proxy is always preferred to a PHP function
            //with the same name (key, current, count, ...), which is callable too.
            $callback = [$proxy, $callback];
        }

        /** @var callable $callback */
        return new AssertionCallback($this->states)->call($callback);
    }
}
