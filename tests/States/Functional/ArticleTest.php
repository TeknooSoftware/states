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

namespace UniAlteri\Tests\States\Functional;

use UniAlteri\States\Loader;
use UniAlteri\Tests\Support\Article\Article;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Loader of the states library.
     *
     * @var \UniAlteri\States\Loader\LoaderInterface
     */
    protected $loader = null;

    /**
     * Load the library State and retrieve its default loader from its bootstrap.
     *
     * @return \UniAlteri\States\Loader\LoaderInterface
     */
    protected function getLoader()
    {
        if (null === $this->loader) {
            $this->loader = include __DIR__.'/../../../src/bootstrap.php';
        }

        return $this->loader;
    }

    /**
     * Functional test on article.
     */
    public function testArticle()
    {
        defined('DS')
            || define('DS', DIRECTORY_SEPARATOR);

        //Register demo namespace
        $this->getLoader()->registerNamespace('\\UniAlteri\\Tests\\Support', UA_STATES_TEST_PATH.DS.'Support');

        $article = new \UniAlteri\Tests\Support\Article();

        //It is a new article, not published, the constructor load the state 'Draft'
        $this->assertEquals(array('StateDefault', 'Draft'), $article->listEnabledStates());
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
        $this->assertEquals(array('StateDefault', 'Published'), $article->listEnabledStates());
        $this->assertEquals('Hello world', $article->getTitle());
        //Method available into Published state
        $this->assertEquals('Lorem <strong>Ipsum</strong>', $article->getFormattedBody());

        //Open a published article
        $article = new \UniAlteri\Tests\Support\Article(
            array(
                'is_published' => true,
                'title' => 'title 2',
                'body' => 'body 2',
            )
        );

        //Already published, so constructor enable state "Default" and "Published"
        $this->assertEquals(array('StateDefault', 'Published'), $article->listEnabledStates());
        $this->assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setTitle('Hello world');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setBody('Lorem [b]Ipsum[/b]');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        $this->assertEquals('title 2', $article->getTitle());

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->getBodySource();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->publishing();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        $this->assertEquals(array('StateDefault', 'Published'), $article->listEnabledStates());
        $this->assertEquals('title 2', $article->getTitle());
        $this->assertEquals('body 2', $article->getFormattedBody());

        $fail = false;
        try {
            $article->getDate();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }
    }

    public function testStatesAlias()
    {
        defined('DS')
            || define('DS', DIRECTORY_SEPARATOR);

        //Register demo namespace
        $this->getLoader()->registerNamespace('\\UniAlteri\\Tests\\Support', UA_STATES_TEST_PATH.DS.'Support');

        $article = new Article();
        $statesList = $article->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            ['Archived', 'Draft', 'Extended', 'Promoted', 'Published', 'StateDefault'],
            $statesList
        );

        $statesList = $article->listEnabledStates();
        sort($statesList);
        $this->assertEquals(['Draft', 'StateDefault'], $statesList);
        $this->assertFalse($article->inState('Archived'));
        $this->assertTrue($article->inState('Draft'));
        $this->assertFalse($article->inState('Extended'));
        $this->assertFalse($article->inState('Promoted'));
        $this->assertFalse($article->inState('Published'));
        $this->assertTrue($article->inState('StateDefault'));

        $article->switchState('Published');

        $this->assertEquals(['Published'], $article->listEnabledStates());
        $this->assertFalse($article->inState('Archived'));
        $this->assertFalse($article->inState('Draft'));
        $this->assertFalse($article->inState('Extended'));
        $this->assertFalse($article->inState('Promoted'));
        $this->assertTrue($article->inState('Published'));
        $this->assertFalse($article->inState('StateDefault'));

        $article->switchState('Promoted');

        $this->assertEquals(['Promoted'], $article->listEnabledStates());
        $this->assertFalse($article->inState('Archived'));
        $this->assertFalse($article->inState('Draft'));
        $this->assertFalse($article->inState('Extended'));
        $this->assertTrue($article->inState('Promoted'));
        $this->assertTrue($article->inState('Published'));
        $this->assertFalse($article->inState('StateDefault'));

        $article->switchState('Archived');

        $this->assertEquals(['Archived'], $article->listEnabledStates());
        $this->assertTrue($article->inState('Archived'));
        $this->assertFalse($article->inState('Draft'));
        $this->assertFalse($article->inState('Extended'));
        $this->assertFalse($article->inState('Promoted'));
        $this->assertTrue($article->inState('Published'));
        $this->assertFalse($article->inState('StateDefault'));

        $article->switchState('Extended');

        $this->assertEquals(['Extended'], $article->listEnabledStates());
        $this->assertFalse($article->inState('Archived'));
        $this->assertFalse($article->inState('Draft'));
        $this->assertTrue($article->inState('Extended'));
        $this->assertTrue($article->inState('Promoted'));
        $this->assertTrue($article->inState('Published'));
        $this->assertFalse($article->inState('StateDefault'));
    }
}
