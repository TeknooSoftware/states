<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\States\Functional;

use \UniAlteri\States\Loader;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \UniAlteri\States\Loader\LoaderInterface
     */
    protected $_loader = null;

    /**
     * @return \UniAlteri\States\Loader\LoaderInterface
     */
    protected function _getLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = include('UniAlteri'.DS.'States'.DS.'bootstrap.php');
        }

        return $this->_loader;
    }

    protected function tearDown()
    {
        if ($this->_loader instanceof Loader\LoaderInterface) {
            spl_autoload_unregister(
                array($this->_loader, 'loadClass')
            );
        }

        parent::tearDown();
    }


    /**
     * Functional test on article
     */
    public function testArticle()
    {
        defined('DS')
            || define('DS', DIRECTORY_SEPARATOR);

        //Register demo namespace
        $this->_getLoader()->registerNamespace('\\UniAlteri\Tests\Support\Article', UA_STATES_TEST_PATH.DS.'Support');

        $article = new UniAlteri\Tests\Support\Article();

        //It is a new article, not published, the constructor load the state 'Draft'
        $article->listEnabledStates();
        //Empty article, getTitle return nothing
        $article->getTitle();
        //Call method of state "Draft" to update the article
        $article->setTitle('Hello world');
        $article->setBody('Lorem [b]Ipsum[/b]');
        //Now article is fulled
        $article->getTitle();
        $article->getBodySource();
        //Publishing method available into Draft state to switch to Published state
        $article->publishing();
        $article->listEnabledStates();
        $article->getTitle();
        //Method available into Published state
        $article->getFormattedBody();

        //Open a published article
        $article = new UniAlteri\Tests\Support\Article(
            array(
                'is_published'  => true,
                'title'         => 'title 2',
                'body'          => 'body 2'
            )
        );

        //Already published, so constructor enable state "Default" and "Published"
        $article->listEnabledStates();
        $article->getTitle();

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setTitle('Hello world');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->setBody('Lorem [b]Ipsum[/b]');
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception');
        }

        $article->getTitle();

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->getBodySource();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception');
        }

        //Method not available, because state Draft is not enabled
        $fail = false;
        try {
            $article->publishing();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception');
        }

        $article->listEnabledStates();
        $article->getTitle();
        $article->getFormattedBody();

        $fail = false;
        try {
            $article->_getDate();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception');
        }
    }
}
