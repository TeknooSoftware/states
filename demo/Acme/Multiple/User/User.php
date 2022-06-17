<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace demo\Acme\Multiple\User;

use demo\Acme\Multiple\User\States\Administrator;
use demo\Acme\Multiple\User\States\Moderator;
use demo\Acme\Multiple\User\States\StateDefault;
use Teknoo\States\Proxy;

/**
 * Proxy User
 * Proxy class of the stated class Proxy.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class User extends Proxy\Standard
{
    protected static function statesListDeclaration(): array
    {
        return [
            Administrator::class,
            Moderator::class,
            StateDefault::class
        ];
    }

    /**
     * To initialize this user with some data.
     */
    public function __construct(
        protected string $userName,
        protected bool $isAdmin = false,
        protected bool $isModerator = false
    ) {
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
}
