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

namespace Teknoo\Tests\Support\Multiple\User\States;

use Teknoo\States\State\AbstractState;

/**
 * State StateDefault
 * Default State for an user
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin User
 */
class StateDefault extends AbstractState
{
    public function getName()
    {
        /*
         * Return the user name of this user.
         *
         * @return string
         */
        return fn () => $this->userName;
    }

    protected function setModerator()
    {
        /*
         * Transform this user as moderator.
         *
         * @param bool $value
         */
        return function ($value): void {
            $this->isModerator = $value;

            if (!empty($this->isModerator)) {
                $this->enableState(Moderator::class);
            }
        };
    }
}
