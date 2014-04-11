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
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\States\Proxy;

use \UniAlteri\States\Proxy;
use \UniAlteri\States\Proxy\Exception;
use \UniAlteri\Tests\Support;

/**
 * Class IntegratedTest
 * Implementation of AbstractProxyTest to test the proxy Proxy\Integrated
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
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
        include_once('UniAlteri/Tests/Support/MockStartupFactory.php');
        //Change the startup factory to the mock for each test
        Support\IntegratedProxy::defineStartupFactoryClassName('\UniAlteri\Tests\Support\MockStartupFactory');
        parent::setUp();
    }

    /**
     * Build a proxy object, into $this->_proxy to test it
     * @return Proxy\ProxyInterface
     */
    protected function _buildProxy()
    {
        $this->_proxy = new Support\IntegratedProxy();
        return $this->_proxy;
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
        } catch (\Exception $e) { }

        $this->fail('Error, the method _initializeProxy() of the trait proxy has not been called');
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
        } catch (\Exception $e) {}

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
        } catch (\Exception $e) {}

        $this->fail('Error, the Integrated Proxy must throw the exception IllegalFactory when the factory class does not implement the interface StartupFactoryInterface');
    }

    public function testInitializationProxyVByFactory()
    {
        Support\MockStartupFactory::$calledProxyObject = null;
        $proxy = new Support\IntegratedProxy();
        $this->assertSame($proxy, Support\MockStartupFactory::$calledProxyObject);
    }
}