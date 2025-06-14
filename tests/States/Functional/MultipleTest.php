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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Functional;

use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\Support\Multiple\User\User;

/**
 * Class MultipleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MultipleTest extends \PHPUnit\Framework\TestCase
{
    public function testMultiple(): void
    {
        //Initialize user
        $simpleUser = new User('simple 1');
        self::assertEquals('simple 1', $simpleUser->getName());
        //Initialize moderator
        $moderator = new User('modo', false, true);
        self::assertEquals('modo', $moderator->getName());
        //Initialize admin
        $administrator = new User('admin', true, true);
        self::assertEquals('admin', $administrator->getName());

        //Method not available, because state Moderator is not enabled
        $this->expectException(MethodNotImplemented::class);
        $simpleUser->isModerator();

        self::assertTrue($moderator->isModerator());
        self::assertTrue($administrator->isModerator());

        //admin transforms the user as modo
        $administrator->setModerator($simpleUser);
        self::assertTrue($simpleUser->isModerator());
    }
}
