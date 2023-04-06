<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
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
use Teknoo\States\Automated\Assertion\Property;

use function end;
use function array_reverse;

/**
 * Set of constraints, passed to automated object to check the value from the defined property
 * and validate the assertions and enabled linked states.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ConstraintsSet implements ConstraintsSetInterface
{
    use ImmutableTrait;

    /**
     * @var ConstraintInterface[]
     */
    private array $constraints = [];

    /**
     * @param array<ConstraintInterface> $constraints
     */
    public function __construct(
        array $constraints,
        private readonly Property $property
    ) {
        $this->uniqueConstructorCheck();

        $this->constraints = array_reverse($constraints);
    }

    private function nextConstraint(): ?ConstraintInterface
    {
        if (!empty($this->constraints)) {
            return end($this->constraints);
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
            $that = new self(
                array_slice($this->constraints, 0, -1),
                $this->property,
            );
            $constraint = $constraint->inConstraintSet($that);
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
        return $this->check($value);
    }
}
