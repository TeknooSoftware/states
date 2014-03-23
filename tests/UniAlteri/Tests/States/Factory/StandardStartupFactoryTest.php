<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\States\Factory;

use \UniAlteri\States\Proxy;
use \UniAlteri\States\Factory;
use \UniAlteri\States\Factory\Exception;
use \UniAlteri\Tests\Support;

class StandardStartupFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The startup factory must throw an exception when the proxy does not implement the proxy interface
     */
    public function testForwardStartupInvalidProxy()
    {
        try {
            Factory\StandardStartupFactory::forwardStartup(new \stdClass());
        } catch (Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the startup factory must throw an exception when the proxy does not implement the proxy interface');
    }

    /**
     * The startup factory must throw an exception when the proxy can not be initialized
     */
    public function testForwardStartupProxyNotInitialized()
    {
        try {
            Factory\StandardStartupFactory::forwardStartup(new Support\VirtualProxy(null));
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the startup factory must throw an exception when the proxy can not be initialized');
    }

    /**
     * Test normal behavior of forward startup
     */
    public function testForwardStartup()
    {
        $factory = new Support\VirtualFactory();
        Factory\StandardStartupFactory::registerFactory('UniAlteri\Tests\Support\VirtualProxy', $factory);
        $proxy = new Support\VirtualProxy(null);
        Factory\StandardStartupFactory::forwardStartup($proxy);
        $this->assertSame($factory->getStartupProxy(), $proxy);
    }

    /**
     * The startup factory class must throw an exception when the identifier is not a valid string
     */
    public function testRegisterFactoryInvalidIdentifier()
    {
        try {
            Factory\StandardStartupFactory::registerFactory(array(), new Support\VirtualFactory());
        } catch (Exception\InvalidArgument $exception) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the startup factory class must throw an exception when the identifier is not a valid string');
    }

    /**
     * The startup factory class must throw an exception when a the registering factory does not implement the factory interface.
     */
    public function testRegisterFactoryInvalidFactory()
    {
        try {
            Factory\StandardStartupFactory::registerFactory('bar', new \stdClass());
        } catch (Exception\IllegalFactory $exception) {
            return;
        } catch (\Exception $e) { }

        $this->fail('Error, the startup factory class must throw an exception when a the registering factory does not implement the factory interface');
    }
}