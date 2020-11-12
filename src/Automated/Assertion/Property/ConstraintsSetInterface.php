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
 * Interface to define a set of constraints, passed to automated object to check the value from the defined property
 * and validate the assertions and enabled linked states.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface ConstraintsSetInterface extends ImmutableInterface
{
    /**
     * First call, by the AutomatedInterface instance to start check on the property's value passed by the Property
     * assertion instance.
     *
     * @param mixed $value
     * @return ConstraintsSetInterface
     */
    public function check(&$value): ConstraintsSetInterface;

    /**
     * Called by the constraint to check the next constraint or validate the property's value.
     *
     * @param mixed $value
     * @return ConstraintsSetInterface
     */
    public function isValid(&$value): ConstraintsSetInterface;
}
