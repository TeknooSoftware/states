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

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Loading lib States
$loader = require_once(dirname(__DIR__).DS.'lib'.DS.'UniAlteri'.DS.'States'.DS.'bootstrap.php');

//Register demo namespace
$loader->registerNamespace('\\demo\\UniAlteri', __DIR__.DS.'UniAlteri');

print 'Uni Alteri - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize new article
print 'Empty article'.PHP_EOL;
$article = new \demo\UniAlteri\Article();

//It is a new article, not published, the constructor load the state 'Draft'
print 'Active states :';
print implode(', ', $article->listActivesStates()).PHP_EOL.PHP_EOL;
//Empty article, getTitle return nothing
print 'title : '.$article->getTitle().PHP_EOL.PHP_EOL;
//Call method of state "Draft" to update the article
print 'set some data'.PHP_EOL;
$article->setTitle('Hello world');
$article->setBody('Lorem [b]Ipsum[/b]');
//Now article is fulled
print 'title : '.$article->getTitle().PHP_EOL;
print 'body : '.$article->getBodySource().PHP_EOL;
//Publishing method available into Draft state to switch to Published state
print PHP_EOL.'publishing...'.PHP_EOL;
$article->publishing();
print 'Active states :';
print implode(', ', $article->listActivesStates()).PHP_EOL.PHP_EOL;
print $article->getTitle().PHP_EOL;
//Method available into Published state
print $article->getFormattedBody().PHP_EOL;

//Open a published article
print 'Open article'.PHP_EOL;
$article = new \demo\UniAlteri\Article(
    array(
        'is_published'  => true,
        'title'         => 'title 2',
        'body'          => 'body 2'
    )
);

//Already published, so constructor enable state "Default" and "Published"
print PHP_EOL.PHP_EOL.'Active states :';
print implode(', ', $article->listActivesStates()).PHP_EOL.PHP_EOL;
print 'title : '.$article->getTitle().PHP_EOL.PHP_EOL;
print 'set some data'.PHP_EOL;

//Method not available, because state Draft is not enable
try {
    $article->setTitle('Hello world');
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}

//Method not available, because state Draft is not enable
try {
    $article->setBody('Lorem [b]Ipsum[/b]');
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}

print 'title : '.$article->getTitle().PHP_EOL;

//Method not available, because state Draft is not enable
try {
    print 'body : '.$article->getBodySource().PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}

//Method not available, because state Draft is not enable
print PHP_EOL.'publishing...'.PHP_EOL;
try {
    $article->publishing();
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}

print 'Active states :';
print implode(', ', $article->listActivesStates()).PHP_EOL.PHP_EOL;
print $article->getTitle().PHP_EOL;
print $article->getFormattedBody().PHP_EOL;

try {
    $article->_getDate();
} catch (\Exception $e) {
    echo $e->getMessage().PHP_EOL;
}