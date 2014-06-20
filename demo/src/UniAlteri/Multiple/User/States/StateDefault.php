<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that is bundled with this package in the file LICENSE.txt.
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

class StateDefault extends States\AbstractState
{
    /**
     * Return the user name of this user
     * @return string
     */
    public function getName()
    {
        return $this->_userName;
    }

    /**
     * Transform this user as moderator
     * @param boolean $value
     */
    protected function _setModerator($value)
    {
        $this->_isModerator = $value;
        if (!empty($this->_isModerator)) {
            $this->enableState('Moderator');
        }
    }
}