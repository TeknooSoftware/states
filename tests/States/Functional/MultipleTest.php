<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MultipleTest extends \PHPUnit\Framework\TestCase
{
    public function testMultiple(): void
    {
        //Initialize user
        $simpleUser = new User('simple 1');
        $this->assertEquals('simple 1', $simpleUser->getName());
        //Initialize moderator
        $moderator = new User('modo', false, true);
        $this->assertEquals('modo', $moderator->getName());
        //Initialize admin
        $administrator = new User('admin', true, true);
        $this->assertEquals('admin', $administrator->getName());

        //Method not available, because state Moderator is not enabled
        $this->expectException(MethodNotImplemented::class);
        $simpleUser->isModerator();

        $this->assertTrue($moderator->isModerator());
        $this->assertTrue($administrator->isModerator());

        //admin transforms the user as modo
        $administrator->setModerator($simpleUser);
        $this->assertTrue($simpleUser->isModerator());
    }
}
