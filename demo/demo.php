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
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace demo;

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

$loader = require_once(dirname(__DIR__).DS.'lib'.DS.'UniAlteri'.DS.'States'.DS.'bootstrap.php');

$loader->registerNamespace('\\demo\\UniAlteri', __DIR__.DS.'UniAlteri');

$article = new \demo\UniAlteri\Article();

print $article->getTitle().PHP_EOL;
$article->enableState('Draft');
$article->setTitle('Hello world');
$article->setBody('Lorem [b]Ipsum[/b]');
print $article->getTitle().PHP_EOL;
print $article->getBodySource().PHP_EOL;
$article->publishing();
print $article->getTitle().PHP_EOL;
print $article->getFormattedBody().PHP_EOL;