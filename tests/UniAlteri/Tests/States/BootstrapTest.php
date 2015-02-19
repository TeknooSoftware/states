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
 * @version     1.0.3
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
use UniAlteri\States\Factory;
use UniAlteri\States\Exception;
use UniAlteri\Tests\Support;

/**
 * Class BootstrapTest
 * Test behavior of the bootstrap file. It initialize the library :
 * * Create an instance of the loader
 * * Create a new instance of the DI Container
 * * Register the service to build new finders for each stated class
 * * Register the service to build new injection closure for each stated classes' methods.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
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
        $this->loader = include 'UniAlteri/States/bootstrap.php';

        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\LoaderInterface', $this->loader);

        //Check if the loader is initialized with a di container
        $container = $this->loader->getDIContainer();
        $this->assertInstanceOf('\\UniAlteri\\States\\DI\\ContainerInterface', $container);

        //Check if required services are present into the di container
        $this->assertTrue($container->testEntry(Loader\FinderInterface::DI_FINDER_SERVICE));
        $this->assertTrue($container->testEntry(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER));

        $fail = false;
        try {
            $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        } catch (Exception\UnavailableFactory $e) {
            $fail = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue($fail, 'Error, the service to create finder must throw exception if the DI Container for the class has not registered factory object');

        //Test behavior of the service to create finder for a stated class
        $container->registerInstance(Factory\FactoryInterface::DI_FACTORY_NAME, new Support\MockFactory());
        $finder = $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\FinderInterface', $finder);

        //Test behavior of the service to create injection closure
        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\UniAlteri\\States\\DI\\InjectionClosureInterface', $injectionClosure);
    }
}
