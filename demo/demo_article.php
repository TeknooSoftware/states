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

use Exception;

$composer = include __DIR__ . '/demo.php';
$composer->setPsr4('demo\\Acme\\', __DIR__.DS.'Acme'.DS);

echo 'Teknoo Software - States library - Demo :'.PHP_EOL.PHP_EOL;
//Initialize new article
echo 'Start with an empty article'.PHP_EOL;
$article = new Acme\Article\Article();

//It is a new article, not published, the constructor load the state 'Draft'
//Empty article, getTitle return nothing
echo 'Title : '.$article->getTitle().PHP_EOL;
//Call method of state "Draft" to update the article
echo SEPARATOR.'Write article'.PHP_EOL;
$article->setTitle('Hello world');
$article->setBody('Lorem [b]Ipsum[/b]');
//Now article is fulled
echo 'Title : '.$article->getTitle().PHP_EOL;
echo 'Body : '.$article->getBodySource().PHP_EOL;
//Publishing method available into Draft state to switch to Published state
echo SEPARATOR.'Publishing...'.PHP_EOL.PHP_EOL;
$article->publishing();
echo $article->getTitle().PHP_EOL;
//Method available into Published state
echo $article->getFormattedBody().PHP_EOL;

//Open a published article
echo SEPARATOR.'Open article'.PHP_EOL;
$article = new Acme\Article\Article(
    ['is_published' => true, 'title' => 'title 2', 'body' => 'body 2']
);

//Already published, so constructor enable state "Default" and "Published"
echo 'title : '.$article->getTitle().PHP_EOL;
echo 'set some data'.PHP_EOL;

//Method not available, because state Draft is not enabled
try {
    $article->setTitle('Hello world');
} catch (Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

//Method not available, because state Draft is not enabled
try {
    $article->setBody('Lorem [b]Ipsum[/b]');
} catch (Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

echo 'title : '.$article->getTitle().PHP_EOL;

//Method not available, because state Draft is not enabled
try {
    echo 'body : '.$article->getBodySource().PHP_EOL;
} catch (Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

//Method not available, because state Draft is not enabled
echo SEPARATOR.'Publishing...'.PHP_EOL.PHP_EOL;
try {
    $article->publishing();
} catch (Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

echo $article->getTitle().PHP_EOL;
echo $article->getFormattedBody().PHP_EOL;

try {
    $article->getDate();
} catch (Exception $e) {
    echo 'Excepted Error : '.RED_COLOR.$e->getMessage().GREEN_COLOR.' GOOD'.RESET_COLOR.PHP_EOL;
}

echo PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
