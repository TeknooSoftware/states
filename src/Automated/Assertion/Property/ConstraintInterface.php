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

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface ConstraintInterface
 * Interface to implement constraint about property to check in the object.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface ConstraintInterface extends ImmutableInterface
{
    /**
     * @param ConstraintsSetInterface $constraintsSet
     * @return ConstraintInterface
     */
    public function inConstraintSet(ConstraintsSetInterface $constraintsSet): ConstraintInterface;

    /**
     * @param mixed &$value
     *
     * @return ConstraintInterface
     */
    public function check(&$value): ConstraintInterface;
}
