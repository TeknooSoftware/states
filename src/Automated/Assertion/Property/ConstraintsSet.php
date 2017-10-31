<?php

declare(strict_types=1);

/**
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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;
use Teknoo\States\Automated\Assertion\Property;

class ConstraintsSet implements ConstraintsSetInterface
{
    use ImmutableTrait;

    /**
     * @var ConstraintInterface[]
     */
    private $constraints = [];

    /**
     * @var Property
     */
    private $property;

    /**
     * ConstraintsSet constructor.
     * @param ConstraintInterface[] $constraints
     * @param Property $property
     */
    public function __construct(array $constraints, Property $property)
    {
        $this->uniqueConstructorCheck();

        $this->constraints = $constraints;
        $this->property = $property;
    }

    /**
     * @return null|ConstraintInterface
     */
    private function nextConstraint(): ?ConstraintInterface
    {
        if (!empty($this->constraints)) {
            return \array_shift($this->constraints);
        }

        return null;
    }

    /**
     * @param $value
     */
    private function processConstraint($value): void
    {
        $constraint = $this->nextConstraint();

        if ($constraint instanceof ConstraintInterface) {
            $constraint = $constraint->inConstraintSet($this);
            $constraint->check($value);
        } else {
            $this->property->isValid();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function check(&$value): ConstraintsSetInterface
    {
        $this->processConstraint($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(&$value): ConstraintsSetInterface
    {
        $this->processConstraint($value);

        return $this;
    }
}
