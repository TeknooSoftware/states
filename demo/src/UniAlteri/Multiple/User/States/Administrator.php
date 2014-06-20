<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace demo\UniAlteri\Multiple\User\States;

use UniAlteri\States\States;

class Administrator extends States\AbstractState
{
    /**
     * Transform an user has moderator
     * @param \demo\UniAlteri\Multiple\User $user
     */
    public function setModerator(\demo\UniAlteri\Multiple\User $user)
    {
        $user->_setModerator(true);
    }
}