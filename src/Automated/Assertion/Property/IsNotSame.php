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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
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
 * Constraint to use with Teknoo\States\Automated\Property to check if a property is not the
 * same value of $this->exceptedValue. (Perform === checks).
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class IsNotSame extends AbstractConstraint
{
    use ImmutableTrait;

    /**
     * @var mixed
     */
    private $exceptedValue;

    /**
     * @param mixed $exceptedValue
     */
    public function __construct($exceptedValue)
    {
        $this->uniqueConstructorCheck();

        $this->exceptedValue = $exceptedValue;
    }

    /**
     * {@inheritdoc}
     */
    public function check(&$value): ConstraintInterface
    {
        if ($this->exceptedValue !== $value) {
            $this->isValid($value);
        }

        return $this;
    }
}
