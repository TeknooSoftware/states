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

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to implement constraint about property to check in the object. A constraint must be immutable. All change
 * must be performed on a clone and will be returned.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface ConstraintInterface extends ImmutableInterface
{
    /*
     * Clone this constraint and inject the Constrain set. This object is not updated, only its clone, to avoid issues
     * on multiples checks.
     */
    public function inConstraintSet(ConstraintsSetInterface $constraintsSet): ConstraintInterface;

    /*
     * Method to call to check if the value, passed by the stated object to this constraint is valid according to rules
     * of this constraint.
     */
    public function check(mixed &$value): ConstraintInterface;
}
