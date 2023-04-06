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
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Multiple\Post;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support\Multiple\Post\States\Deleted;
use Teknoo\Tests\Support\Multiple\Post\States\Published;
use Teknoo\Tests\Support\Multiple\Post\States\StateDefault;

/**
 * Proxy Class
 * Proxy for the stated class "Post"
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class Post extends Proxy\Standard
{
    protected static function statesListDeclaration(): array
    {
        return [
            Deleted::class,
            Published::class,
            StateDefault::class,
        ];
    }
}
