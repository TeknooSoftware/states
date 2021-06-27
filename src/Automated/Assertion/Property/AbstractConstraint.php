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

/**
 * Abstract class to build constraint to create an instance with a ConstraintSet injected and dispatch isValid() to it.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
