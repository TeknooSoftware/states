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

use \UniAlteri\States\DI;
use \UniAlteri\States\Loader;
use \UniAlteri\States\Proxy;
use \UniAlteri\States\Factory;
use \UniAlteri\States\Factory\Exception;
use \UniAlteri\Tests\Support;

abstract class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DI\Container
     */
    protected $_container = null;

    /**
     * @var Support\VirtualFinder
     */
    protected $_virtualFinder = null;

    /**
     * Initialize container used into Factory
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_container = new DI\Container();
        $this->_virtualFinder = new Support\VirtualFinder('', '');
        $this->_container->registerInstance(Loader\FinderInterface::DI_FINDER_SERVICE, $this->_virtualFinder);
    }

    /**
     * Return the Factory Object Interface
     * @param boolean $populateContainer to populate di container of this factory
     * @return Factory\FactoryInterface
     */
    abstract public function getFactoryObject($populateContainer=true);

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = $this->getFactoryObject(false);
        try {
            $object->setDIContainer(new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the DI Container is not valid');
    }

    /**
     * Test behavior for methods Set And GetDiContainer
     */
    public function testSetAndGetDiContainer()
    {
        $object = $this->getFactoryObject(false);
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\VirtualDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    /**
     * Test the exception of the library when the proxy object doest not implement the exception
     */
    public function testExceptionBadProxyStartup()
    {
        try {
            $this->getFactoryObject()->startup(array());
        } catch(Exception\IllegalProxy $exception) {
            return;
        }

        $this->fail('Error, if the proxy does not implement the proxy object, the factory must throw an exception');
    }

    /**
     * Test exceptions thrown when the stated class has no default state
     */
    public function testExceptionDefaultStateNotAvailableInStartup()
    {
        try {
            $this->_virtualFinder->ignoreDefaultState = true;
            $this->getFactoryObject()->startup(new Support\VirtualProxy(null));
        } catch(Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not a default state, the factory must throw an exception StateNotFound');
    }

    /**
     * Test exceptions thrown when the stated class has not the required starting state
     */
    public function testExceptionRequiredStateNotAvailableInStartup()
    {
        try{
            $this->_virtualFinder->ignoreDefaultState = false;
            $this->getFactoryObject()->startup(new Support\VirtualProxy(null), 'NonExistentState');
        } catch(Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not the required starting state, the factory must throw an exception StateNotFound');
    }

    public function testListAvailableStateInStartup()
    {
        $proxy = new Support\VirtualProxy(null);
        $this->getFactoryObject()->startup($proxy);
        $this->assertEquals(
            array(
                'VirtualState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'VirtualState2',
                'VirtualState3'
            ),
            $proxy->listAvailableStates()
        );
    }

    public function testDefaultStateAutomaticallySelectedInStartup()
    {
        $proxy = new Support\VirtualProxy(null);
        $this->getFactoryObject()->startup($proxy);
        $this->assertEquals($proxy->listActivesStates(), array('Default'));
    }

    public function testRequiredStateSelectedInStartup()
    {
        $proxy = new Support\VirtualProxy(null);
        $this->getFactoryObject()->startup($proxy, 'VirtualState1');
        $this->assertEquals($proxy->listActivesStates(), array('VirtualState1'));
    }

    /**
     * Test exceptions thrown when the stated class has no default state
     */
    public function testExceptionDefaultStateNotAvailable()
    {
        try {
            $this->_virtualFinder->ignoreDefaultState = true;
            $this->getFactoryObject()->build();
        } catch(Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not a default state, the factory must throw an exception StateNotFound');
    }

    /**
     * Test exceptions thrown when the stated class has not the required starting state
     */
    public function testExceptionRequiredStateNotAvailable()
    {
        try{
            $this->_virtualFinder->ignoreDefaultState = false;
            $this->getFactoryObject()->build(false, 'NonExistentState');
        } catch(Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not the required starting state, the factory must throw an exception StateNotFound');
    }

    public function testListAvailableState()
    {
        $proxy = $this->getFactoryObject()->build();
        $this->assertEquals(
            array(
                'VirtualState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'VirtualState2',
                'VirtualState3'
            ),
            $proxy->listAvailableStates()
        );
    }

    public function testDefaultStateAutomaticallySelected()
    {
        $proxy = $this->getFactoryObject()->build();
        $this->assertEquals($proxy->listActivesStates(), array('Default'));
    }

    public function testRequiredStateSelected()
    {
        $proxy = $this->getFactoryObject()->build(null, 'VirtualState1');
        $this->assertEquals($proxy->listActivesStates(), array('VirtualState1'));
    }

    public function testPassedArguments()
    {
        $args = array('foo' => 'bar');
        $proxy = $this->getFactoryObject()->build($args);
        $this->assertSame($args, $proxy->args);
    }
}