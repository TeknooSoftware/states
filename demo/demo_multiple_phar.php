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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace demo;

error_reporting(E_ALL | E_STRICT);

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Loading lib States
$loader = require_once dirname(__DIR__).DS.'src'.DS.'UniAlteri'.DS.'States'.DS.'bootstrap_composer.php';

//Register demo namespace
$loader->registerNamespace('\\demo\\Acme\\Multiple', 'phar://'.__DIR__.DS.'Acme'.DS.'multiple.phar');

print 'Uni Alteri - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize user
print 'user'.PHP_EOL;
$simpleUser = new Acme\Multiple\User\User('simple 1');
print 'get name : '.$simpleUser->getName().PHP_EOL;
//Initialize moderator
print 'moderator'.PHP_EOL;
//You call also directly the stated class name and not the proxy
$moderator = new Acme\Multiple\User('modo', false, true);
print 'get name : '.$moderator->getName().PHP_EOL;
//Initialize admin
print 'admin'.PHP_EOL;
$administrator = new Acme\Multiple\User\User('admin', true, true);
print 'get name : '.$administrator->getName().PHP_EOL.PHP_EOL;

//Method not available, because state Moderator is not enabled
try {
    print 'user is moderator '.$simpleUser->isModerator().PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}
print 'modo is moderator '.$moderator->isModerator().PHP_EOL;
print 'admin is moderator '.$administrator->isModerator().PHP_EOL;

print 'admin transforms the user as modo :'.PHP_EOL;
$administrator->setModerator($simpleUser);
print 'user is moderator '.$simpleUser->isModerator().PHP_EOL;

//Initialize another stated class of this phar
$newPost = new Acme\Multiple\Post\Post();
print 'object post created'.PHP_EOL;
