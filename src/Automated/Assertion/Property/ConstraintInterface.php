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

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to implement constraint about property to check in the object. A constraint must be immutable. All change
 * must be performed on a clone and will be returned.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
