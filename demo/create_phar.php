<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace demo;

error_reporting(E_ALL | E_STRICT);

defined('DS')
|| define('DS', DIRECTORY_SEPARATOR);

//Compute Path for this Phar
$singlePath = __DIR__.DS.'src'.DS.'UniAlteri'.DS.'Single';
$singlePharPath = __DIR__.DS.'UniAlteri'.DS.'Single.phar';
if (file_exists($singlePharPath)) {
    unlink($singlePharPath);
}
//Build phat
$phar = new \Phar($singlePharPath, 0, 'Single.phar');
$phar->buildFromDirectory($singlePath);

//Compute Path for this Phar
$multiplePath = __DIR__.DS.'src'.DS.'UniAlteri'.DS.'Multiple';
$multiplePharPath = __DIR__.DS.'UniAlteri'.DS.'multiple.phar';
if (file_exists($multiplePharPath)) {
    unlink($multiplePharPath);
}
//Build phat
$phar = new \Phar($multiplePharPath, 0, 'multiple.phar');
$phar->buildFromDirectory($multiplePath);