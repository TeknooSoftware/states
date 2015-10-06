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
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace demo;

$loader = include 'demo.php';

//Register demo namespace
$loader->registerNamespace('\\demo\\Acme\\Article', __DIR__.DS.'Acme'.DS.'Article');

print 'Uni Alteri - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize new article
print 'Start with an empty article'.PHP_EOL;
$article = new Acme\Article\Article();

//It is a new article, not published, the constructor load the state 'Draft'
print 'Enabled states : ';
print GREEN_COLOR.implode(', ', $article->listEnabledStates()).RESET_COLOR.PHP_EOL;
//Empty article, getTitle return nothing
print 'Title : '.$article->getTitle().PHP_EOL;
//Call method of state "Draft" to update the article
print SEPARATOR.'Write article'.PHP_EOL;
$article->setTitle('Hello world');
$article->setBody('Lorem [b]Ipsum[/b]');
//Now article is fulled
print 'Title : '.$article->getTitle().PHP_EOL;
print 'Body : '.$article->getBodySource().PHP_EOL;
//Publishing method available into Draft state to switch to Published state
print SEPARATOR.'Publishing...'.PHP_EOL.PHP_EOL;
$article->publishing();
print 'Enabled states :';
print GREEN_COLOR.implode(', ', $article->listEnabledStates()).RESET_COLOR.PHP_EOL;
print $article->getTitle().PHP_EOL;
//Method available into Published state
print $article->getFormattedBody().PHP_EOL;

//Open a published article
print SEPARATOR.'Open article'.PHP_EOL;
//You call also directly the stated class name and not the proxy
$article = new Acme\Article(
    array(
        'is_published' => true,
        'title' => 'title 2',
        'body' => 'body 2',
    )
);

//Already published, so constructor enable state "Default" and "Published"
print PHP_EOL.'Enabled states : ';
print GREEN_COLOR.implode(', ', $article->listEnabledStates()).RESET_COLOR.PHP_EOL;
print 'title : '.$article->getTitle().PHP_EOL;
print 'set some data'.PHP_EOL;

//Method not available, because state Draft is not enabled
try {
    $article->setTitle('Hello world');
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

//Method not available, because state Draft is not enabled
try {
    $article->setBody('Lorem [b]Ipsum[/b]');
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

print 'title : '.$article->getTitle().PHP_EOL;

//Method not available, because state Draft is not enabled
try {
    print 'body : '.$article->getBodySource().PHP_EOL;
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

//Method not available, because state Draft is not enabled
print SEPARATOR.'Publishing...'.PHP_EOL.PHP_EOL;
try {
    $article->publishing();
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

print 'Enabled states : ';
print GREEN_COLOR.implode(', ', $article->listEnabledStates()).RESET_COLOR.PHP_EOL;
print $article->getTitle().PHP_EOL;
print $article->getFormattedBody().PHP_EOL;

try {
    $article->getDate();
} catch (\Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

print PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
