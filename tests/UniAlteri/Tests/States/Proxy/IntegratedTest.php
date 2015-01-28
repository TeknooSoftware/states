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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.1
 */
namespace UniAlteri\Tests\States\Proxy;

use UniAlteri\States\Proxy;
use UniAlteri\States\Proxy\Exception;
use UniAlteri\Tests\Support;

/**
 * Class IntegratedTest
 * Implementation of AbstractProxyTest to test the proxy Proxy\Integrated
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class IntegratedTest extends AbstractProxyTest
{
    /**
     * For these tests, we use Support\IntegratedProxy instead of Proxy\Integrated to use the
     * Support\MockStartupFactory instead of Factory\StandardStartupFactory
     */
    protected function setUp()
    {
        //Change the startup factory to the mock for each test
        Support\IntegratedProxy::defineStartupFactoryClassName('\UniAlteri\Tests\Support\MockStartupFactory');
        parent::setUp();
    }

    /**
     * Build a proxy object, into $this->proxy to test it
     * @return Proxy\ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new Support\IntegratedProxy();

        return $this->proxy;
    }

    /**
     * Test if the class initialize its vars from the trait constructor
     */
    public function testInitializationProxyVar()
    {
        $proxy = new Support\IntegratedProxy();
        try {
            $this->assertSame(array(), $proxy->listAvailableStates());

            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the method initializeProxy() of the trait proxy has not been called');
    }

    /**
     * Test if the factory to use to initialize the proxy does not exist, proxy throws an exception
     */
    public function testInitializationProxyVByFactoryFactoryDoestNotExist()
    {
        Support\IntegratedProxy::defineStartupFactoryClassName('badName');
        try {
            new Support\IntegratedProxy();
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the Integrated Proxy must throw the exception UnavailableFactory when the factory class is not available');
    }

    /**
     * Test if the factory to use to initialize the proxy does not implement the method, proxy throws an exception
     */
    public function testInitializationProxyVByFactoryFactoryInvalid()
    {
        Support\IntegratedProxy::defineStartupFactoryClassName('DateTime');
        try {
            new Support\IntegratedProxy();
        } catch (Exception\IllegalFactory $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the Integrated Proxy must throw the exception IllegalFactory when the factory class does not implement the interface StartupFactoryInterface');
    }

    public function testInitializationProxyVByFactory()
    {
        Support\MockStartupFactory::$calledProxyObject = null;
        $proxy = new Support\IntegratedProxy();
        $this->assertSame($proxy, Support\MockStartupFactory::$calledProxyObject);
    }
}
