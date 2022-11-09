<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Functional;

use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Archived;
use Teknoo\Tests\Support\Article\Article\Draft;
use Teknoo\Tests\Support\Article\Article\Extended;
use Teknoo\Tests\Support\Article\Article\Promoted;
use Teknoo\Tests\Support\Article\Article\Published;
use Teknoo\Tests\Support\Article\Article\StateDefault;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ArticleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Functional test on article.
     */
    public function testArticle()
    {
        $article = new Article();

        //It is a new article, not published, the constructor load the state 'Draft'
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
        self::assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setTitle('Hello world');
        } catch (\Exception) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setBody('Lorem [b]Ipsum[/b]');
        } catch (\Exception) {
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
        } catch (\Exception) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->publishing();
        } catch (\Exception) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        self::assertEquals('title 2', $article->getTitle());
        self::assertEquals('body 2', $article->getFormattedBody());

        $fail = false;
        try {
            $article->getDate();
        } catch (\Exception) {
            $fail = true;
        }

        if (!$fail) {
            self::fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }
    }

    public function testStatesFullQualifiedClassName()
    {
        $article = new Article();

        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function ($states) use (&$called) {
                self::assertEquals([Draft::class, StateDefault::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function ($states) use (&$called) {
                self::assertEquals([Draft::class, StateDefault::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);

        $article->switchState(Published::class);

        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called) {
                self::assertEquals([Published::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function () {
                self::fail();
            })
        );


        $article->switchState(Promoted::class);


        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function ($states) use (&$called) {
                self::assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called) {
                self::assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function () {
                self::fail();
            })
        );


        $article->switchState(Archived::class);


        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function ($states) use (&$called) {
                self::assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called) {
                self::assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function () {
                self::fail();
            })
        );


        $article->switchState(Extended::class);


        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function () {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function () {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function ($states) use (&$called) {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function ($states) use (&$called) {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called) {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function () {
                self::fail();
            })
        );
    }
}
