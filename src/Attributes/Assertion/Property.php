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
use Teknoo\States\Automated\Assertion\Property as PropertyAssertion;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_slice;
use function class_exists;
use function is_a;
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
final class Property implements AssertionInterface
{
    private PropertyAssertion $property;

    /**
     * @param string|array<int, string> $states
     * @param array{0: string, 1: class-string<PropertyAssertion\ConstraintInterface>, ...} ...$withs
     */
    public function __construct(
        string|array $states,
        array ...$withs,
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
        }

        $this->property = new PropertyAssertion($states);

        foreach ($withs as $with) {
            if (!is_string($with[0])) {
                throw new InvalidArgumentException('Each first `with` argument must be a property name');
            }

            if (!is_string($with[1]) || !is_a($with[1], PropertyAssertion\ConstraintInterface::class, true)) {
                throw new InvalidArgumentException('Each second `with` argument must be a constraint class name');
            }

            $this->property = $this->property->with($with[0], new $with[1](...array_slice($with, 2)));
        }
    }

    public function getAssertion(ProxyInterface $proxy): AutomatedAssertionInterface
    {
        return $this->property;
    }
}
