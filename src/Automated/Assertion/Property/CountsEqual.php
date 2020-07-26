<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;

/**
 * Constraint to use with Teknoo\States\Automated\Property to check if a property
 * is a countable or an array and has count of elements set in parameter
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CountsEqual extends AbstractConstraint
{
    use ImmutableTrait;

    private int $exceptedCount;

    public function __construct(int $exceptedCount)
    {
        $this->uniqueConstructorCheck();

        $this->exceptedCount = $exceptedCount;
    }

    /**
     * {@inheritdoc}
     */
    public function check(&$value): ConstraintInterface
    {
        if (
            $value instanceof \Countable
            && $value->count() === $this->exceptedCount
        ) {
            $this->isValid($value);

            return $this;
        }

        if (
            \is_array($value)
            && \count($value) === $this->exceptedCount
        ) {
            $this->isValid($value);
        }

        return $this;
    }
}
