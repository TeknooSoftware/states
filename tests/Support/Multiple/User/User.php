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
namespace Teknoo\Tests\Support\Multiple\User;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support\Multiple\User\States\Administrator;
use Teknoo\Tests\Support\Multiple\User\States\Moderator;
use Teknoo\Tests\Support\Multiple\User\States\StateDefault;

/**
 * Proxy User
 * Proxy class of the stated class Proxy
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class User extends Proxy\Standard
{
    /**
     * Username of this user.
     *
     * @var string
     */
    protected $userName = '';

    /**
     * To know if this user is an admin.
     *
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * To know if this user is a moderator.
     *
     * @var bool
     */
    protected $isModerator = false;

    /**
     * To initialize this user with some data.
     *
     * @param string $username
     * @param bool   $isAdmin
     * @param bool   $isModerator
     */
    public function __construct($username, $isAdmin = false, $isModerator = false)
    {
        //Register options
        $this->userName = $username;
        $this->isAdmin = $isAdmin;
        $this->isModerator = $isModerator;
        //Initialize user
        parent::__construct();
        //Load states
        if (!empty($this->isAdmin)) {
            $this->enableState(Administrator::class);
            $this->enableState(Moderator::class);
        }

        if (!empty($this->isModerator)) {
            $this->enableState(Moderator::class);
        }
    }

    public static function statesListDeclaration(): array
    {
        return [
            Administrator::class,
            Moderator::class,
            StateDefault::class
        ];
    }
}
