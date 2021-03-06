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
namespace Teknoo\Tests\States\Functional;

use Teknoo\Tests\Support\Multiple\User\User;

/**
 * Class MultipleTest
 * Functional test number 1, from demo article.
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
class MultipleTest extends \PHPUnit\Framework\TestCase
{
    public function testMultiple()
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
        $fail = false;
        try {
            $simpleUser->isModerator();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        self::assertTrue($moderator->isModerator());
        self::assertTrue($administrator->isModerator());

        //admin transforms the user as modo
        $administrator->setModerator($simpleUser);
        self::assertTrue($simpleUser->isModerator());
    }
}
