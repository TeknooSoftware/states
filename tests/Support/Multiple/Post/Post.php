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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Post extends Proxy\Standard
{
    public static function statesListDeclaration(): array
    {
        return [
            Deleted::class,
            Published::class,
            StateDefault::class,
        ];
    }
}
