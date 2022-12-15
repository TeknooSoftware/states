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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class User extends Proxy\Standard
{
    /**
     * To initialize this user with some data.
     *
     * @param string $userName
     * @param bool   $isAdmin
     * @param bool   $isModerator
     */
    public function __construct(protected string $userName, protected bool $isAdmin = false, protected bool $isModerator = false)
    {
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

    protected static function statesListDeclaration(): array
    {
        return [
            Administrator::class,
            Moderator::class,
            StateDefault::class,
        ];
    }
}
