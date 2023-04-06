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

/**
 * Abstract class to build constraint to create an instance with a ConstraintSet injected and dispatch isValid() to it.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractConstraint implements ConstraintInterface
{
    private ?ConstraintsSetInterface $constraintsSet = null;

    public function inConstraintSet(ConstraintsSetInterface $constraintsSet): ConstraintInterface
    {
        $that = clone $this;

        $that->constraintsSet = $constraintsSet;

        return $that;
    }

    /*
     * To return the success of the check to the ConstraintSet and continue the workflow
     */
    protected function isValid(mixed &$value): ConstraintInterface
    {
        if ($this->constraintsSet instanceof ConstraintsSetInterface) {
            $this->constraintsSet->isValid($value);
        }

        return $this;
    }
}
