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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace demo;

error_reporting(E_ALL | E_STRICT);

defined('DS')
|| define('DS', DIRECTORY_SEPARATOR);

//Compute Path for this Phar
$multiplePath = __DIR__.DS.'Acme'.DS.'Multiple';
$multiplePharPath = __DIR__.DS.'Acme'.DS.'multiple.phar';
if (file_exists($multiplePharPath)) {
    unlink($multiplePharPath);
}
//Build phat
$phar = new \Phar($multiplePharPath, 0, 'multiple.phar');
$phar->buildFromDirectory($multiplePath);
