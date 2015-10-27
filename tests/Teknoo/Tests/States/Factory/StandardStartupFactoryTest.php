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
 */

namespace Teknoo\Tests\States\Factory;

use Teknoo\States\Proxy;
use Teknoo\States\Factory;
use Teknoo\States\Factory\Exception;
use Teknoo\Tests\Support;

/**
 * Class StandardStartupFactoryTest
 * Test the exception behavior of the start up standard factory.
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
class StandardStartupFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare test, reinitialize the StandardStartupFactory.
     */
    protected function setUp()
    {
        Factory\StandardStartupFactory::reset();
        parent::setUp();
    }

    /**
     * The startup factory must throw an exception when the proxy does not implement the proxy interface.
     */
    public function testForwardStartupInvalidProxy()
    {
        try {
            Factory\StandardStartupFactory::forwardStartup(new \stdClass());
        } catch (Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the startup factory must throw an exception when the proxy does not implement the proxy interface');
    }

    /**
     * The startup factory must throw an exception when the proxy cannot be initialized.
     */
    public function testForwardStartupProxyNotInitialized()
    {
        try {
            Factory\StandardStartupFactory::forwardStartup(new Support\MockProxy(null));
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the startup factory must throw an exception when the proxy cannot be initialized');
    }

    /**
     * Test normal behavior of forward startup.
     */
    public function testForwardStartup()
    {
        $factory = new Support\MockFactory();
        Factory\StandardStartupFactory::registerFactory('Teknoo\Tests\Support\MockProxy', $factory);
        $proxy = new Support\MockProxy(null);
        Factory\StandardStartupFactory::forwardStartup($proxy);
        $this->assertSame($factory->getStartupProxy(), $proxy);
    }

    /**
     * The startup factory class must throw an exception when the identifier is not a valid string.
     */
    public function testRegisterFactoryInvalidIdentifier()
    {
        try {
            Factory\StandardStartupFactory::registerFactory(array(), new Support\MockFactory());
        } catch (Exception\InvalidArgument $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the startup factory class must throw an exception when the identifier is not a valid string');
    }

    /**
     * The startup factory class must throw an exception when the registering factory does not implement the factory interface.
     */
    public function testRegisterFactoryInvalidFactory()
    {
        try {
            Factory\StandardStartupFactory::registerFactory('bar', new \stdClass());
        } catch (Exception\IllegalFactory $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the startup factory class must throw an exception when the registering factory does not implement the factory interface');
    }

    /**
     * Test Factory\StandardStartupFactory::listRegisteredFactory if its return all initialized factory.
     */
    public function testListRegisteredFactory()
    {
        $factory = new Support\MockFactory();
        Factory\StandardStartupFactory::registerFactory('Teknoo\Tests\Support\MockProxy1', $factory);
        Factory\StandardStartupFactory::reset();
        Factory\StandardStartupFactory::registerFactory('Teknoo\Tests\Support\MockProxy2', $factory);
        Factory\StandardStartupFactory::registerFactory('Teknoo\Tests\Support\MockProxy3', $factory);
        $this->assertEquals(
            array(
                'Teknoo\Tests\Support\MockProxy2',
                'Teknoo\Tests\Support\MockProxy3',
            ),
            Factory\StandardStartupFactory::listRegisteredFactory()
        );
    }

    /**
     * Test Factory\StandardStartupFactory::listRegisteredFactory if its return all initialized factory.
     */
    public function testListRegisteredFactoryEmpty()
    {
        Factory\StandardStartupFactory::reset();
        $this->assertEquals(
            array(),
            Factory\StandardStartupFactory::listRegisteredFactory()
        );
    }
}
