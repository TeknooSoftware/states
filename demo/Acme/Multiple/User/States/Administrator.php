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
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace demo\Acme\Multiple\User\States;

use demo\Acme\Multiple\User\User;
use Teknoo\States\State\AbstractState;

/**
 * State Administrator
 * State for an user with admin right.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin User
 */
class Administrator extends AbstractState
{
    public function setModerator()
    {
        /**
         * Transform an user has moderator.
         *
         * @param User $user
         */
        return function (User $user): void {
            $user->setModerator(true);
        };
    }
}
