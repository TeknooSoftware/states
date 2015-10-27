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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * Test behavior of the bootstrap file. It initialize the library :
 * * Create an instance of the loader
 * * Create a new instance of the DI Container
 * * Register the service to build new finders for each stated class
 * * Register the service to build new injection closure for each stated classes' methods
 */

namespace Teknoo\Tests\States;

use Teknoo\States;
use Teknoo\States\Loader;
use Teknoo\States\Factory;
use Teknoo\States\Exception;
use Teknoo\Tests\Support;

/**
 * Class BootstrapTest
 * Test behavior of the bootstrap file. It initialize the library :
 * * Create an instance of the loader
 * * Create a new instance of the DI Container
 * * Register the service to build new finders for each stated class
 * * Register the service to build new injection closure for each stated classes' methods.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Teknoo\States\Loader\LoaderInterface
     */
    protected $loader = null;

    protected function tearDown()
    {
        if ($this->loader instanceof \Teknoo\States\Loader\LoaderInterface) {
            spl_autoload_unregister(
                array($this->loader, 'loadClass')
            );
        }

        parent::tearDown();
    }

    public function testLoaderInitialisation()
    {
        //Call the bootstrap, it returns the loader
        $this->loader = include 'Teknoo/States/bootstrap.php';

        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\LoaderInterface', $this->loader);

        //Check if the loader is initialized with a di container
        $container = $this->loader->getDIContainer();
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container);

        //Check if the factory repository has been created
        $this->assertTrue($container->testEntry(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container->get(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));

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
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\FinderInterface', $finder);

        //Test behavior of the service to create injection closure
        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosureInterface', $injectionClosure);
        if ('5.6' > PHP_VERSION) {
            $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosure', $injectionClosure);
        } else {
            $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosurePHP56', $injectionClosure);
        }
    }

    public function testLoaderComposerInitialisation()
    {
        //Call the bootstrap, it returns the loader
        $this->loader = include 'Teknoo/States/bootstrap_composer.php';
        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\LoaderInterface', $this->loader);
        //Check if the loader is initialized with a di container
        $container = $this->loader->getDIContainer();
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container);
        //Check if the factory repository has been created
        $this->assertTrue($container->testEntry(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container->get(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
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
        $factory = new Support\MockFactory();
        $factory->initialize('className', 'path1');
        $container->registerInstance(Factory\FactoryInterface::DI_FACTORY_NAME, $factory);
        $finder = $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\FinderInterface', $finder);

        //Test behavior of the service to create injection closure
        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosureInterface', $injectionClosure);
        if ('5.6' > PHP_VERSION) {
            $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosure', $injectionClosure);
        } else {
            $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosurePHP56', $injectionClosure);
        }
    }

    public function testLoaderComposerInitialisationBefore56()
    {
        if ('5.6' > PHP_VERSION) {
            $this->markTestSkipped('Version of PHP is not supported for this injection closure');

            return;
        }

        defined('DISABLE_PHP_FLOC_OPERATOR') || define('DISABLE_PHP_FLOC_OPERATOR', true);
        //Call the bootstrap, it returns the loader
        $this->loader = include 'Teknoo/States/bootstrap_composer.php';
        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\LoaderInterface', $this->loader);
        //Check if the loader is initialized with a di container
        $container = $this->loader->getDIContainer();
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container);
        //Check if the factory repository has been created
        $this->assertTrue($container->testEntry(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container->get(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
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
        $factory = new Support\MockFactory();
        $factory->initialize('className', 'path1');
        $container->registerInstance(Factory\FactoryInterface::DI_FACTORY_NAME, $factory);
        $finder = $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\FinderInterface', $finder);

        //Test behavior of the service to create injection closure
        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosureInterface', $injectionClosure);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosure', $injectionClosure);
    }

    public function testLoaderInitialisationBefore56()
    {
        if ('5.6' > PHP_VERSION) {
            $this->markTestSkipped('Version of PHP is not supported for this injection closure');

            return;
        }

        defined('DISABLE_PHP_FLOC_OPERATOR') || define('DISABLE_PHP_FLOC_OPERATOR', true);

        //Call the bootstrap, it returns the loader
        $this->loader = include 'Teknoo/States/bootstrap.php';

        //Check if the loader implements the good interface
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\LoaderInterface', $this->loader);

        //Check if the loader is initialized with a di container
        $container = $this->loader->getDIContainer();
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container);

        //Check if the factory repository has been created
        $this->assertTrue($container->testEntry(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\ContainerInterface', $container->get(Factory\FactoryInterface::DI_FACTORY_REPOSITORY));

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
        $this->assertInstanceOf('\\Teknoo\\States\\Loader\\FinderInterface', $finder);

        //Test behavior of the service to create injection closure
        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosureInterface', $injectionClosure);
        $this->assertInstanceOf('\\Teknoo\\States\\DI\\InjectionClosure', $injectionClosure);
    }
}
