<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
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
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion\Property;

use Countable;
use Teknoo\Immutable\ImmutableTrait;

use function count;
use function is_array;

/**
 * Constraint to use with `Teknoo\States\Automated\Property` to check if a property
 * is a countable or an array and has count of elements set in parameter
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class CountsEqual extends AbstractConstraint
{
    use ImmutableTrait;

    public function __construct(private readonly int $exceptedCount)
    {
        $this->uniqueConstructorCheck();
    }

    public function check(mixed &$value): ConstraintInterface
    {
        if (
            $value instanceof Countable
            && $value->count() === $this->exceptedCount
        ) {
            $this->isValid($value);

            return $this;
        }

        if (
            is_array($value)
            && count($value) === $this->exceptedCount
        ) {
            $this->isValid($value);
        }

        return $this;
    }
}
