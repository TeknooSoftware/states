<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

namespace demo;

error_reporting(E_ALL | E_STRICT);

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Loading lib States
$loader = require_once(dirname(__DIR__).DS.'lib'.DS.'UniAlteri'.DS.'States'.DS.'bootstrap.php');

//Register demo namespace
$loader->registerNamespace('\\demo\\UniAlteri', __DIR__.DS.'UniAlteri');
$loader->registerNamespace('\\demo\\UniAlteri\\Multiple', 'phar://'.__DIR__.DS.'UniAlteri'.DS.'multiple.phar');

print 'Uni Alteri - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize user
print 'user'.PHP_EOL;
$simpleUser = new UniAlteri\Multiple\User('simple 1');
print 'get name : '.$simpleUser->getName().PHP_EOL;
//Initialize moderator
print 'moderator'.PHP_EOL;
$moderator = new UniAlteri\Multiple\User('modo', false, true);
print 'get name : '.$moderator->getName().PHP_EOL;
//Initialize admin
print 'admin'.PHP_EOL;
$administrator = new UniAlteri\Multiple\User('admin', true, true);
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
$newPost = new UniAlteri\Multiple\Post();
print 'object post created'.PHP_EOL;