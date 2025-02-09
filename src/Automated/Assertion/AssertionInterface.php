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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion;

use Teknoo\Immutable\ImmutableInterface;
use Teknoo\States\Automated\AutomatedInterface;

/**
 * Interface to build assertion, needed by automated stated class to determine currently active states.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
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
