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
 */

namespace UniAlteri\Tests\States\Factory;

use UniAlteri\States\DI;
use UniAlteri\States\Loader;
use UniAlteri\States\Proxy;
use UniAlteri\States\Factory;
use UniAlteri\States\Factory\Exception;
use UniAlteri\Tests\Support;

/**
 * Class AbstractFactoryTest.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
abstract class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock finder used for test.
     *
     * @var Support\MockFinder
     */
    protected $virtualFinder = null;

    /**
     * @var \ArrayAccess
     */
    protected $repository;

    /**
     * Initialize container used into Factory.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->repository = new \ArrayObject([]);
    }

    /**
     * Return the Factory Object Interface.
     *
     * @param Loader\FinderInterface $finder
     *
     * @return Factory\FactoryInterface
     */
    abstract public function getFactoryObject(Loader\FinderInterface $finder);

    /**
     * Test the factory behavior to build a new finder object from the service registered into its DI.
     */
    public function testGetFinder()
    {
        $this->assertInstanceOf(
            'UniAlteri\States\Loader\FinderInterface',
            $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->getFinder()
        );
    }

    /**
     * Test the behavior of the method getStatedClassName() with values (stated class name and path) defined
     * by the loading during factory initialization.
     */
    public function testGetStatedClassName()
    {
        $factory = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'));
        $this->assertEquals('My\Stated\Class', $factory->getStatedClassName());
    }

    /**
     * Test the behavior of the method initialize() called by the loading during factory initialization
     * - Prerequisite : Finder service (to create new Finder instance dedicated for its stated class)
     * The factory must find and load the proxy class (but not create an instance) :
     * If the proxy class is not defined for the stated class, it must create an alias from the standard or integrated proxy.
     */
    public function testInitialize()
    {
        $virtualFinder = new Support\MockFinder('My\Stated\Class', 'path/to/my/class');

        $factory = $this->getFactoryObject($virtualFinder);

        $this->assertTrue($virtualFinder->proxyHasBeenLoaded());
        $this->assertSame($factory, $this->repository[$factory->getStatedClassName()]);
    }

    /**
     * Test exceptions thrown when the stated class has no default state.
     */
    public function testBehaviorDefaultStateNotAvailableInStartup()
    {
        Support\MockFinder::$ignoreDefaultState = true;
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->startup($proxy);
        $this->assertEmpty($proxy->listEnabledStates());
    }

    /**
     * Test exceptions thrown when the stated class has not the required starting state.
     */
    public function testExceptionRequiredStateNotAvailableInStartup()
    {
        try {
            Support\MockFinder::$ignoreDefaultState = false;
            $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->startup(new Support\MockProxy(null), 'NonExistentState');
        } catch (Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not the required starting state, the factory must throw an exception StateNotFound');
    }

    /**
     * Test if the factory can retrieve from the finder the list of available states for the stated class.
     */
    public function testListAvailableStateInStartup()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))
            ->startup($proxy);
        $this->assertEquals(
            array(
                'MockState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'MockState2',
                'MockState3',
            ),
            $proxy->listAvailableStates()
        );
    }

    /**
     * Test if the factory can retrieve from the finder the list of available states for the stated class.
     */
    public function testListAvailableStateInStartupWithInheritanceMotherNotFound()
    {
        $factoryMother = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class1'));
        $factoryMother->getFinder();

        //Finder
        $factoryDaughter = $this->getFactoryObject(new Support\MockFinderInheritance('My\Stated\Class2', 'path/to/my/class2'));
        $factoryDaughter->getFinder();

        $proxy = new Support\MockProxyChild(null);
        try {
            $factoryDaughter->startup($proxy);
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());

            return;
        }

        $this->fail('Error, the factory must throw an exception if it can not found parent factory in registry');
    }

    /**
     * Test if the factory can retrieve from the finder the list of available states for the stated class.
     */
    public function testListAvailableStateInStartupWithInheritance()
    {
        $factoryMother = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class1', 'path/to/my/class1'));
        $factoryMother->getFinder();

        //Finder
        $factoryDaughter = $this->getFactoryObject(new Support\MockFinderInheritance('My\Stated\Class2', 'path/to/my/class2'));
        $factoryDaughter->getFinder();

        $proxy = new Support\MockProxyChild(null);
        $factoryDaughter->startup($proxy);
        $this->assertEquals(
            array(
                'MockState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'MockState4',
                'MockState2',
                'MockState3',
            ),
            $proxy->listAvailableStates()
        );

        $this->assertFalse($proxy->getState('MockState1')->isPrivateMode());
        $this->assertFalse($proxy->getState(Proxy\ProxyInterface::DEFAULT_STATE_NAME)->isPrivateMode());
        $this->assertFalse($proxy->getState('MockState4')->isPrivateMode());
        $this->assertTrue($proxy->getState('MockState2')->isPrivateMode());
        $this->assertTrue($proxy->getState('MockState3')->isPrivateMode());
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the default state if there is no defined startup state.
     */
    public function testDefaultStateAutomaticallySelectedInStartup()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->startup($proxy);
        $this->assertEquals($proxy->listEnabledStates(), array('StateDefault'));
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the required state if there is defined startup state.
     */
    public function testRequiredStateSelectedInStartup()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinderInheritance('My\Stated\Class1', 'path/to/my/class'))->startup($proxy, 'MockState1');
        $this->assertEquals($proxy->listEnabledStates(), array('MockState1'));
    }

    /**
     * Check if the factory register all available states of the stated class in the new proxy
     * (Finder use ArrayObject instead of array to return the list of states).
     */
    public function testListAvailableStateInStartupWithArrayObject()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinderWithArray('My\Stated\Class', 'path/to/my/class'))->startup($proxy);
        $this->assertEquals(
            array(
                'MockState1',
                'MockState2',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'MockState3',
            ),
            $proxy->listAvailableStates()
        );
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the default state if there is no defined startup state
     * (Finder use ArrayObject instead of array to return the list of states).
     */
    public function testDefaultStateAutomaticallySelectedInStartupWithArrayObject()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinderWithArray('My\Stated\Class', 'path/to/my/class'))->startup($proxy);
        $this->assertEquals($proxy->listEnabledStates(), array('StateDefault'));
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the required state if there is defined startup state
     * (Finder use ArrayObject instead of array to return the list of states).
     */
    public function testRequiredStateSelectedInStartupWithArrayObject()
    {
        $proxy = new Support\MockProxy(null);
        $this->getFactoryObject(new Support\MockFinderWithArray('My\Stated\Class', 'path/to/my/class'))->startup($proxy, 'MockState1');
        $this->assertEquals($proxy->listEnabledStates(), array('MockState1'));
    }

    /**
     * Test exceptions thrown when the stated class has no default state.
     */
    public function testBehaviorDefaultStateNotAvailable()
    {
        Support\MockFinder::$ignoreDefaultState = true;
        $proxy = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build();
        $this->assertEmpty($proxy->listEnabledStates());
    }

    /**
     * Test exceptions thrown when the stated class has not the required starting state.
     */
    public function testExceptionRequiredStateNotAvailable()
    {
        try {
            Support\MockFinder::$ignoreDefaultState = false;
            $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build(false, 'NonExistentState');
        } catch (Exception\StateNotFound $exception) {
            return;
        }

        $this->fail('Error, if the stated class has not the required starting state, the factory must throw an exception StateNotFound');
    }

    /**
     * Check if the factory register all available states of the stated class in the new proxy.
     */
    public function testListAvailableState()
    {
        $proxy = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build();
        $this->assertEquals(
            array(
                'MockState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'MockState2',
                'MockState3',
            ),
            $proxy->listAvailableStates()
        );
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the default state if there is no defined startup state.
     */
    public function testDefaultStateAutomaticallySelected()
    {
        $proxy = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build();
        $this->assertEquals($proxy->listEnabledStates(), array('StateDefault'));
    }

    /**
     * Check if the factory, when it initialize a new proxy, enable the required state if there is defined startup state.
     */
    public function testRequiredStateSelected()
    {
        $proxy = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build(null, 'MockState1');
        $this->assertEquals($proxy->listEnabledStates(), array('MockState1'));
    }

    /**
     * Check if the factory pass arguments to the.
     */
    public function testPassedArguments()
    {
        $args = array('foo' => 'bar');
        $proxy = $this->getFactoryObject(new Support\MockFinder('My\Stated\Class', 'path/to/my/class'))->build($args);
        $this->assertSame($args, $proxy->args);
    }
}
