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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace demo\Acme\Multiple\User\States;

use Teknoo\States\State\AbstractState;

/**
 * State StateDefault
 * Default State for an user.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
        return function() {
            return $this->userName;
        };
    }

    protected function setModerator()
    {
        /**
         * Transform this user as moderator.
         *
         * @param bool $value
         */
        return function ($value) {
            $this->isModerator = $value;

            if (!empty($this->isModerator)) {
                $this->enableState('Moderator');
            }
        };
    }
}
