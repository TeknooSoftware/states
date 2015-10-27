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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace demo;

$loader = include 'demo.php';

//Register demo namespace
$loader->registerNamespace('\\demo\\Acme\\Multiple', 'phar://'.__DIR__.DS.'Acme'.DS.'multiple.phar');

print 'Teknoo Software - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize user
print 'User : ';
$simpleUser = new Acme\Multiple\User\User('simple');
print 'get name : '.GREEN_COLOR.$simpleUser->getName().RESET_COLOR.PHP_EOL;
//Initialize moderator
print 'Moderator : ';
//You call also directly the stated class name and not the proxy
$moderator = new Acme\Multiple\User('modo', false, true);
print 'get name : '.GREEN_COLOR.$moderator->getName().RESET_COLOR.PHP_EOL;
//Initialize admin
print 'Admin : ';
$administrator = new Acme\Multiple\User\User('admin', true, true);
print 'get name : '.GREEN_COLOR.$administrator->getName().RESET_COLOR.PHP_EOL.PHP_EOL;

//Method not available, because state Moderator is not enabled
print 'User is moderator : ';
try {
    print $simpleUser->isModerator().PHP_EOL;
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}
print 'Modo is moderator : '.GREEN_COLOR.$moderator->isModerator().RESET_COLOR.PHP_EOL;
print 'Admin is moderator : '.GREEN_COLOR.$administrator->isModerator().RESET_COLOR.PHP_EOL;

print SEPARATOR.'Admin transforms the user as modo :'.PHP_EOL;
$administrator->setModerator($simpleUser);
print 'User is moderator : '.GREEN_COLOR.$simpleUser->isModerator().RESET_COLOR.PHP_EOL;

//Initialize another stated class of this phar
$newPost = new Acme\Multiple\Post\Post();
print GREEN_COLOR.'Object post created'.RESET_COLOR.PHP_EOL;

print PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
