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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Proxy;

use Teknoo\States\Proxy;
use Teknoo\States\Proxy\Exception;
use Teknoo\Tests\Support;

/**
 * Class IntegratedTest
 * Implementation of AbstractProxyTest to test the proxy Proxy\Integrated.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers Teknoo\States\Proxy\ProxyTrait
 * @covers Teknoo\States\Proxy\Integrated
 * @covers Teknoo\States\Proxy\IntegratedTrait
 * @covers Teknoo\States\Proxy\ArrayAccessTrait
 * @covers Teknoo\States\Proxy\IteratorTrait
 * @covers Teknoo\States\Proxy\MagicCallTrait
 * @covers Teknoo\States\Proxy\SerializableTrait
 */
class IntegratedTest extends AbstractProxyTest
{
    /**
     * For these tests, we use Support\IntegratedProxy instead of Proxy\Integrated to use the
     * Support\MockStartupFactory instead of Factory\StandardStartupFactory.
     */
    protected function setUp()
    {
        //Change the startup factory to the mock for each test
        Support\IntegratedProxy::defineStartupFactoryClassName('\Teknoo\Tests\Support\MockStartupFactory');
        parent::setUp();
    }

    /**
     * Build a proxy object, into $this->proxy to test it.
     *
     * @return Proxy\ProxyInterface
     */
    protected function buildProxy()
    {
        $this->proxy = new Support\IntegratedProxy();

        return $this->proxy;
    }

    /**
     * Test if the class initialize its vars from the trait constructor.
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
     * Test if the factory to use to initialize the proxy does not exist, proxy throws an exception.
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
     * Test if the factory to use to initialize the proxy does not implement the method, proxy throws an exception.
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
