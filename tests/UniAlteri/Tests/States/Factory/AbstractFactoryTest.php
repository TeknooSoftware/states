<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 17/10/13
 * Time: 10:48
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
        $this->_virtualFinder = new Support\VirtualFinder();
        $this->_container->registerInstance(Loader\FinderInterface::DI_FINDER_NAME, $this->_virtualFinder);
    }

    /**
     * Return the Factory Object Interface
     * @return Factory\FactoryInterface
     */
    abstract public function getFactoryObject();

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