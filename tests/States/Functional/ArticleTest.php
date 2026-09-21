<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Functional;

use Closure;
use PHPUnit\Framework\TestCase;
use Teknoo\States\State\AbstractState;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Archived;
use Teknoo\Tests\Support\Article\Article\Draft;
use Teknoo\Tests\Support\Article\Article\Extended;
use Teknoo\Tests\Support\Article\Article\Promoted;
use Teknoo\Tests\Support\Article\Article\Published;
use Teknoo\Tests\Support\Article\Article\StateDefault;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\Support\States\NamedStateInterface;

use function strtoupper;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ArticleTest extends TestCase
{
    /**
     * @return Article
     */
    public function buildObject()
    {
        return new Article();
    }

    /**
     * Functional test on article.
     */
    public function testArticle(): void
    {
        $article = $this->buildObject();

        //It is a new article, not published, the constructor load the state 'Draft'
        //Empty article, getTitle return nothing
        $this->assertEquals('', $article->getTitle());
        //Call method of state "Draft" to update the article
        $article->setTitle('Hello world');
        $article->setBody('Lorem [b]Ipsum[/b]');
        //Now article is fulled
        $this->assertEquals('Hello world', $article->getTitle());
        $this->assertEquals('Lorem [b]Ipsum[/b]', $article->getBodySource());
        //Publishing method available into Draft state to switch to Published state
        $article->publishing();
        $this->assertEquals('Hello world', $article->getTitle());
        //Method available into Published state
        $this->assertEquals('Lorem <strong>Ipsum</strong>', $article->getFormattedBody());

        //Open a published article
        $article = new Article(
            ['is_published' => true, 'title' => 'title 2', 'body' => 'body 2']
        );

        //Already published, so constructor enable state "Default" and "Published"
        $this->assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->setTitle('Hello world');

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->setBody('Lorem [b]Ipsum[/b]');

        $this->assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->getBodySource();

        //Method not available, because state Draft is not enabled
        $this->expectException(MethodNotImplemented::class);
        $article->publishing();

        $this->assertEquals('title 2', $article->getTitle());
        $this->assertEquals('body 2', $article->getFormattedBody());

        $this->expectException(MethodNotImplemented::class);
        $article->getDate();
    }

    public function testStatesFullQualifiedClassName(): void
    {
        $article = $this->buildObject();
        $articleClass = $article::class;

        $this->assertInstanceOf($articleClass, $article->isInState([Archived::class], function (): never {
            self::fail();
        }));
        $called = false;
        $this->assertInstanceOf($articleClass, $article->isInState([Draft::class], function ($states) use (&$called): void {
            $this->assertEquals([Draft::class, StateDefault::class], $states);
            $called = true;
        }));
        $this->assertTrue($called);
        $this->assertInstanceOf($articleClass, $article->isInState([Extended::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Promoted::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Published::class], function (): never {
            self::fail();
        }));
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState(
                [StateDefault::class],
                function ($states) use (&$called): void {
                    $this->assertEquals([Draft::class, StateDefault::class], $states);
                    $called = true;
                }
            )
        );
        $this->assertTrue($called);

        $article->switchState(Published::class);

        $this->assertInstanceOf($articleClass, $article->isInState([Archived::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Draft::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Extended::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Promoted::class], function (): never {
            self::fail();
        }));
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState(
                [Published::class],
                function ($states) use (&$called): void {
                    $this->assertEquals([Published::class], $states);
                    $called = true;
                }
            )
        );
        $this->assertTrue($called);
        $this->assertInstanceOf($articleClass, $article->isInState([StateDefault::class], function (): never {
            self::fail();
        }));


        $article->switchState(Promoted::class);


        $this->assertInstanceOf($articleClass, $article->isInState([Archived::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Draft::class], function (): never {
            self::fail();
        }));
        $this->assertInstanceOf($articleClass, $article->isInState([Extended::class], function (): never {
            self::fail();
        }));
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Promoted::class], function ($states) use (&$called): void {
                $this->assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                $this->assertEquals([Promoted::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );


        $article->switchState(Archived::class);


        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Archived::class], function ($states) use (&$called): void {
                $this->assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Extended::class], function (): never {
                self::fail();
            })
        );
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Promoted::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                $this->assertEquals([Archived::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );


        $article->switchState(Extended::class);


        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Archived::class], function (): never {
                self::fail();
            })
        );
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Draft::class], function (): never {
                self::fail();
            })
        );
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Extended::class], function ($states) use (&$called): void {
                $this->assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Promoted::class], function ($states) use (&$called): void {
                $this->assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $called = false;
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([Published::class], function ($states) use (&$called): void {
                $this->assertEquals([Extended::class], $states);
                $called = true;
            })
        );
        $this->assertTrue($called);
        $this->assertInstanceOf(
            $articleClass,
            $article->isInState([StateDefault::class], function (): never {
                self::fail();
            })
        );
    }

    /**
     * A state's method switching states, then calling another state's method, must not be kept into the proxy's
     * cache of called methods : at the second call its state is disabled, so the method must not be available.
     */
    public function testDisabledStateIsNotExecutedFromCalledMethodCache(): void
    {
        $article = $this->buildObject();
        $article->setTitle('Hello world');

        //Method of the state Draft : it enables the state Published, then calls getTitle() in the new states
        $this->assertSame('Hello world', $article->publishAndGetTitle());

        //The state Draft is now disabled, its methods are not available, even from the proxy's cache
        $this->expectException(MethodNotImplemented::class);
        $article->publishAndGetTitle();
    }

    /**
     * A stated class instance must be serializable whether methods of its states have already been called or not
     * (states are shared between all instances of a same stated class : a call on any instance must not forbid
     * the serialization of others). Enabled states are restored with the object.
     */
    public function testArticleIsSerializableAfterStateMethodCalls(): void
    {
        $article = $this->buildObject();
        $article->setTitle('Hello world');
        $article->setBody('Lorem [b]Ipsum[/b]');
        $this->assertSame('Hello world', $article->getTitle());

        $unserializedArticle = unserialize(serialize($article));
        $this->assertInstanceOf($article::class, $unserializedArticle);

        //States enabled before the serialization are always enabled
        $this->assertSame('Hello world', $unserializedArticle->getTitle());
        $this->assertSame('Lorem [b]Ipsum[/b]', $unserializedArticle->getBodySource());

        //States can be switched on the restored object
        $unserializedArticle->publishing();
        $this->assertSame('Lorem <strong>Ipsum</strong>', $unserializedArticle->getFormattedBody());

        //A new instance, never called, shares its states with previous instances
        $this->assertInstanceOf($article::class, unserialize(serialize($this->buildObject())));
    }

    /**
     * A method of a state can be a static closure, when it does not need the instance : it is executed without
     * error nor PHP warning ("Cannot bind an instance to a static closure"), at each call.
     */
    public function testStaticClosureOfAStateIsExecuted(): void
    {
        $article = $this->buildObject();

        $this->assertNull($article->returnStaticClosure());
        $this->assertNull($article->returnStaticClosure());
    }

    /**
     * A state can be defined with an anonymous class, registered and enabled with the name of an interface
     * implemented by this state.
     */
    public function testAnonymousStateRegisteredWithAnInterfaceName(): void
    {
        $article = $this->buildObject();
        $article->setTitle('Hello world');

        $state = new class (false, $article::class) extends AbstractState implements NamedStateInterface {
            public function getUpperTitle(): Closure
            {
                return fn (): string => strtoupper((string) $this->getTitle());
            }
        };

        $article->registerState(NamedStateInterface::class, $state);
        $article->enableState(NamedStateInterface::class);

        $this->assertSame('HELLO WORLD', $article->getUpperTitle());
        $this->assertSame('HELLO WORLD', $article->getUpperTitle());

        $called = false;
        $article->isInState([NamedStateInterface::class], function () use (&$called): void {
            $called = true;
        });
        $this->assertTrue($called);
    }
}
