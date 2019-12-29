<?php

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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support\Multiple\User\States;

use Teknoo\States\State\AbstractState;
use Teknoo\Tests\Support\Multiple\User\User;

/**
 * State Administrator
 * State for an user with admin right
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin User
 */
class Administrator extends AbstractState
{
    public function setModerator()
    {
        /*
         * Transform an user has moderator.
         *
         * @param User $user
         */
        return function (User $user) {
            $user->setModerator(true);
        };
    }
}
