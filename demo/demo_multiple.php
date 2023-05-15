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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace demo;

use demo\Acme\Multiple\User\User;

$composer = include __DIR__ . '/demo.php';
$composer->setPsr4('demo\\Acme\\', __DIR__.DS.'Acme'.DS);

echo 'Teknoo Software - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize user
echo 'User : ';
$simpleUser = new Acme\Multiple\User\User('simple');
echo 'get name : '.GREEN_COLOR.$simpleUser->getName().RESET_COLOR.PHP_EOL;
//Initialize moderator
echo 'Moderator : ';
//You call also directly the stated class name and not the proxy
$moderator = new User('modo', false, true);
echo 'get name : '.GREEN_COLOR.$moderator->getName().RESET_COLOR.PHP_EOL;
//Initialize admin
echo 'Admin : ';
$administrator = new Acme\Multiple\User\User('admin', true, true);
echo 'get name : '.GREEN_COLOR.$administrator->getName().RESET_COLOR.PHP_EOL.PHP_EOL;

//Method not available, because state Moderator is not enabled
echo 'User is moderator : ';
try {
    echo $simpleUser->isModerator().PHP_EOL;
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}
echo 'Modo is moderator : '.GREEN_COLOR.$moderator->isModerator().RESET_COLOR.PHP_EOL;
echo 'Admin is moderator : '.GREEN_COLOR.$administrator->isModerator().RESET_COLOR.PHP_EOL;

echo SEPARATOR.'Admin transforms the user as modo :'.PHP_EOL;
$administrator->setModerator($simpleUser);
echo 'User is moderator : '.GREEN_COLOR.$simpleUser->isModerator().RESET_COLOR.PHP_EOL;

//Initialize another stated class of this phar
$newPost = new Acme\Multiple\Post\Post();
echo GREEN_COLOR.'Object post created'.RESET_COLOR.PHP_EOL;

echo PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
