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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion;

use Teknoo\Immutable\ImmutableInterface;
use Teknoo\States\Automated\AutomatedInterface;

/**
 * Interface to build assertion, needed by automated stated class to determine currently active states.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface AssertionInterface extends ImmutableInterface
{
    /*
     * To check the assertion's rules with the passed proxy. All assertions must be immutable, so this method must
     * return a cloned object if it's states has been changed.
     */
    public function check(AutomatedInterface $proxy): AssertionInterface;

    /*
     * Called to set the assertion as valid and enable linked state.
     */
    public function isValid(): AssertionInterface;
}
