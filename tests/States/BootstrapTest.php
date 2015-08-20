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
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * Test behavior of the bootstrap file. It initialize the library :
 * * Create an instance of the loader
 * * Create a new instance of the DI Container
 * * Register the service to build new finders for each stated class
 * * Register the service to build new injection closure for each stated classes' methods
 */

namespace UniAlteri\Tests\States;

use UniAlteri\States;
use UniAlteri\States\Loader;

/**
 * Class BootstrapTest
 * Test behavior of the bootstrap file. It initialize the library :
 * * Create an instance of the loader
 * * Create a new instance of the DI Container
 * * Register the service to build new finders for each stated class
 * * Register the service to build new injection closure for each stated classes' methods.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \UniAlteri\States\Loader\LoaderInterface
     */
    protected $loader = null;

    protected function tearDown()
    {
        if ($this->loader instanceof \UniAlteri\States\Loader\LoaderInterface) {
            spl_autoload_unregister(
                array($this->loader, 'loadClass')
            );
        }

        parent::tearDown();
    }

    public function testLoaderInitialisation()
    {
        //Call the bootstrap, it returns the loader
        $this->loader = include __DIR__.'/../../src/bootstrap.php';

        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\LoaderInterface', $this->loader);

        $this->assertInstanceOf('\\Closure', $this->loader->getFinderFactory());

        $finder = $this->loader->getFinderFactory()->__invoke('class', 'path');
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\FinderInterface', $finder);
        if ($finder instanceof Loader\FinderInterface) {
            $this->assertEquals('class', $finder->getStatedClassName());
        }
    }
}
