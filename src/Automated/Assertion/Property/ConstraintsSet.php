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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;
use Teknoo\States\Automated\Assertion\Property;

use function array_pop;
use function array_reverse;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ConstraintsSet implements ConstraintsSetInterface
{
    use ImmutableTrait;

    /**
     * @var ConstraintInterface[]
     */
    private array $constraints;

    private Property $property;

    /**
     * @param array<ConstraintInterface> $constraints
     */
    public function __construct(array $constraints, Property $property)
    {
        $this->uniqueConstructorCheck();


        $this->constraints = array_reverse($constraints);
        $this->property = $property;
    }

    private function nextConstraint(): ?ConstraintInterface
    {
        if (!empty($this->constraints)) {
            return array_pop($this->constraints);
        }

        return null;
    }

    /**
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    private function processConstraint(mixed $value): void
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
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function check(mixed &$value): ConstraintsSetInterface
    {
        $this->processConstraint($value);

        return $this;
    }

    /**
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function isValid(mixed &$value): ConstraintsSetInterface
    {
        $this->processConstraint($value);

        return $this;
    }
}
