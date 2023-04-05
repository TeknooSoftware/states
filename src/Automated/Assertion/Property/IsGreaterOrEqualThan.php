<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;

/**
 * Constraint to use with `Teknoo\States\Automated\Property` to check if a property
 * is great or equal to of `$this->exceptedValue`. (Perform <= checks).
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class IsGreaterOrEqualThan extends AbstractConstraint
{
    use ImmutableTrait;

    public function __construct(private readonly mixed $exceptedValue)
    {
        $this->uniqueConstructorCheck();
    }

    public function check(mixed &$value): ConstraintInterface
    {
        if ($this->exceptedValue <= $value) {
            $this->isValid($value);
        }

        return $this;
    }
}
