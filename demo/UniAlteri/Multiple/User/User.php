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
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

namespace demo\UniAlteri\Multiple\User;

use UniAlteri\States\Proxy;

/**
 * Proxy User
 * Proxy class of the stated class Proxy
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class User extends Proxy\Integrated
{
    /**
     * Username of this user
     * @var string
     */
    protected $userName = '';

    /**
     * To know if this user is an admin
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * To know if this user is a moderator
     * @var bool
     */
    protected $isModerator = false;

    /**
     * To initialize this user with some data
     * @param string $username
     * @param bool   $isAdmin
     * @param bool   $isModerator
     */
    public function __construct($username, $isAdmin=false, $isModerator=false)
    {
        //Register options
        $this->userName = $username;
        $this->isAdmin = $isAdmin;
        $this->isModerator = $isModerator;
        //Initialize user
        parent::__construct();
        //Load states
        if (!empty($this->isAdmin)) {
            $this->enableState('Administrator');
            $this->enableState('Moderator');
        }

        if (!empty($this->isModerator)) {
            $this->enableState('Moderator');
        }
    }
}
