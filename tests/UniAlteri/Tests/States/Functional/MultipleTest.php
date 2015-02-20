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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.1.0
 */

namespace UniAlteri\Tests\States\Functional;

use UniAlteri\States\Loader;

/**
 * Class MultipleTest
 * Functional test number 1, from demo article.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MultipleTest extends \PHPUnit_Framework_TestCase
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
            $this->loader = include 'UniAlteri'.DS.'States'.DS.'bootstrap.php';
        }

        return $this->loader;
    }

    /**
     * Create the PHAR multiple.phar for the test if this file does not exist.
     */
    protected function setUp()
    {
        $multiplePharPath = UA_STATES_TEST_PATH.DIRECTORY_SEPARATOR.'Support'
                                               .DIRECTORY_SEPARATOR.'multiple.phar';

        if (!file_exists($multiplePharPath)) {
            //Compute Path for this Phar
            $multiplePath = UA_STATES_TEST_PATH.DIRECTORY_SEPARATOR.'Support'
                                               .DIRECTORY_SEPARATOR.'src'
                                               .DIRECTORY_SEPARATOR.'Multiple';

            //Build phat
            $phar = new \Phar($multiplePharPath, 0, 'multiple.phar');
            $phar->buildFromDirectory($multiplePath);
        }

        parent::setUp();
    }

    /**
     * Unload the loader from SPL Autoload to not interfere with others tests.
     */
    protected function tearDown()
    {
        if ($this->loader instanceof Loader\LoaderInterface) {
            spl_autoload_unregister(
                array($this->loader, 'loadClass')
            );
        }

        parent::tearDown();
    }

    public function testMultiple()
    {
        defined('DS')
            || define('DS', DIRECTORY_SEPARATOR);

        //Loading lib States
        $loader = $this->getLoader();

        //Register demo namespace
        $loader->registerNamespace('\\UniAlteri\\Tests\\Support', UA_STATES_TEST_PATH.DS.'Support');
        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Multiple', 'phar://'.UA_STATES_TEST_PATH.DS.'Support'.DS.'multiple.phar');

        //Initialize user
        $simpleUser = new \UniAlteri\Tests\Support\Multiple\User('simple 1');
        $this->assertEquals('simple 1', $simpleUser->getName());
        //Initialize moderator
        $moderator = new \UniAlteri\Tests\Support\Multiple\User('modo', false, true);
        $this->assertEquals('modo', $moderator->getName());
        //Initialize admin
        $administrator = new \UniAlteri\Tests\Support\Multiple\User('admin', true, true);
        $this->assertEquals('admin', $administrator->getName());

        //Method not available, because state Moderator is not enabled
        $fail = false;
        try {
            $simpleUser->isModerator();
        } catch (\Exception $e) {
            $fail = true;
        }

        if (!$fail) {
            $this->fail('Error, the lib must throw an exception because the method is not available in enabled states');
        }

        $this->assertTrue($moderator->isModerator());
        $this->assertTrue($administrator->isModerator());

        //admin transforms the user as modo
        $administrator->setModerator($simpleUser);
        $this->assertTrue($simpleUser->isModerator());

        //Initialize another stated class of this phar
        $newPost = new \UniAlteri\Tests\Support\Multiple\Post();
        $this->assertInstanceOf('\UniAlteri\Tests\Support\Multiple\Post', $newPost);
    }
}
