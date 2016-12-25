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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\States\Functional;

use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\States\Archived;
use Teknoo\Tests\Support\Article\States\Draft;
use Teknoo\Tests\Support\Article\States\Extended;
use Teknoo\Tests\Support\Article\States\Promoted;
use Teknoo\Tests\Support\Article\States\Published;
use Teknoo\Tests\Support\Article\States\StateDefault;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Functional test on article.
     */
    public function testArticle()
    {
        $article = new Article();

        //It is a new article, not published, the constructor load the state 'Draft'
        self::assertEquals([StateDefault::class, Draft::class], $article->listEnabledStates());
        //Empty article, getTitle return nothing
        self::assertEquals('', $article->getTitle());
        //Call method of state "Draft" to update the article
        $article->setTitle('Hello world');
        $article->setBody('Lorem [b]Ipsum[/b]');
        //Now article is fulled
        self::assertEquals('Hello world', $article->getTitle());
        self::assertEquals('Lorem [b]Ipsum[/b]', $article->getBodySource());
        //Publishing method available into Draft state to switch to Published state
        $article->publishing();
        self::assertEquals([StateDefault::class, Published::class], $article->listEnabledStates());
        self::assertEquals('Hello world', $article->getTitle());
        //Method available into Published state
        self::assertEquals('Lorem <strong>Ipsum</strong>', $article->getFormattedBody());

        //Open a published article
        $article = new Article(
            array(
                'is_published' => true,
                'title' => 'title 2',
                'body' => 'body 2',
            )
        );

        //Already published, so constructor enable state "Default" and "Published"
        self::assertEquals([StateDefault::class, Published::class], $article->listEnabledStates());
        self::assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setTitle('Hello world');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setBody('Lorem [b]Ipsum[/b]');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        self::assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->getBodySource();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->publishing();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        self::assertEquals([StateDefault::class, Published::class], $article->listEnabledStates());
        self::assertEquals('title 2', $article->getTitle());
        self::assertEquals('body 2', $article->getFormattedBody());

        $fail = false;
        try {
            $article->getDate();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }
    }

    public function testStatesFullQualifiedClassName()
    {
        $article = new Article();
        $statesList = $article->listAvailableStates();
        sort($statesList);
        self::assertEquals(
            [Archived::class, Draft::class, Extended::class, Promoted::class, Published::class, StateDefault::class],
            $statesList
        );

        $statesList = $article->listEnabledStates();
        sort($statesList);
        self::assertEquals([Draft::class, StateDefault::class], $statesList);
        self::assertFalse($article->inState(Archived::class));
        self::assertTrue($article->inState(Draft::class));
        self::assertFalse($article->inState(Extended::class));
        self::assertFalse($article->inState(Promoted::class));
        self::assertFalse($article->inState(Published::class));
        self::assertTrue($article->inState(StateDefault::class));

        $article->switchState(Published::class);

        self::assertEquals([Published::class], $article->listEnabledStates());
        self::assertFalse($article->inState(Archived::class));
        self::assertFalse($article->inState(Draft::class));
        self::assertFalse($article->inState(Extended::class));
        self::assertFalse($article->inState(Promoted::class));
        self::assertTrue($article->inState(Published::class));
        self::assertFalse($article->inState(StateDefault::class));

        $article->switchState(Promoted::class);

        self::assertEquals([Promoted::class], $article->listEnabledStates());
        self::assertFalse($article->inState(Archived::class));
        self::assertFalse($article->inState(Draft::class));
        self::assertFalse($article->inState(Extended::class));
        self::assertTrue($article->inState(Promoted::class));
        self::assertTrue($article->inState(Published::class));
        self::assertFalse($article->inState(StateDefault::class));

        $article->switchState(Archived::class);

        self::assertEquals([Archived::class], $article->listEnabledStates());
        self::assertTrue($article->inState(Archived::class));
        self::assertFalse($article->inState(Draft::class));
        self::assertFalse($article->inState(Extended::class));
        self::assertFalse($article->inState(Promoted::class));
        self::assertTrue($article->inState(Published::class));
        self::assertFalse($article->inState(StateDefault::class));

        $article->switchState(Extended::class);

        self::assertEquals([Extended::class], $article->listEnabledStates());
        self::assertFalse($article->inState(Archived::class));
        self::assertFalse($article->inState(Draft::class));
        self::assertTrue($article->inState(Extended::class));
        self::assertTrue($article->inState(Promoted::class));
        self::assertTrue($article->inState(Published::class));
        self::assertFalse($article->inState(StateDefault::class));
    }
}
