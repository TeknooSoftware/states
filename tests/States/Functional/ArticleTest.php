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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
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
use Teknoo\States\Proxy\Exception\MethodNotImplemented;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ArticleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Functional test on article.
     */
    public function testArticle(): void
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
            ['is_published' => true, 'title' => 'title 2', 'body' => 'body 2']
        );

        //Already published, so constructor enable state "Default" and "Published"
        self::assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->setTitle('Hello world');

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->setBody('Lorem [b]Ipsum[/b]');

        self::assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->getBodySource();

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->publishing();

        self::assertEquals('title 2', $article->getTitle());
        self::assertEquals('body 2', $article->getFormattedBody());

        $this->expectException(MethodNotImplemented::class);
        $article->getDate();
    }

    public function testStatesFullQualifiedClassName(): void
    {
        $article = new Article();

        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function ($states) use (&$called): void {
                self::assertEquals([Draft::class, StateDefault::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function ($states) use (&$called): void {
                self::assertEquals([Draft::class, StateDefault::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);

        $article->switchState(Published::class);

        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                self::assertEquals([Published::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );


        $article->switchState(Promoted::class);


        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function ($states) use (&$called): void {
                self::assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                self::assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );


        $article->switchState(Archived::class);


        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function ($states) use (&$called): void {
                self::assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                self::assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );


        $article->switchState(Extended::class);


        self::assertInstanceOf(
            Article::class,
            $article->isInState([Archived::class], function (): never {
                self::fail();
            })
        );
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Extended::class], function ($states) use (&$called): void {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Promoted::class], function ($states) use (&$called): void {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        $called = false;
        self::assertInstanceOf(
            Article::class,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                self::assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        self::assertTrue($called);
        self::assertInstanceOf(
            Article::class,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );
    }
}
