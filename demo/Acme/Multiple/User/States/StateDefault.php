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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
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
 * State StateDefault
 * Default State for an user.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin User
 */
class StateDefault extends AbstractState
{
    public function getName()
    {
        /**
         * Return the user name of this user.
         *
         * @return string
         */
        return fn() => $this->userName;
    }

    protected function setModerator()
    {
        /**
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
