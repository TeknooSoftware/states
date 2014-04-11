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

namespace UniAlteri\Tests\States\Loader;

use UniAlteri\States\Loader;
use UniAlteri\States\States;
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

class FinderStandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader\FinderInterface
     */
    protected $_finder = null;

    /**
     * Stated class into a folder
     * @var string
     */
    protected $_statedClass1Path = null;

    /**
     * Stated class into a phar
     * @var string
     */
    protected $_statedClass2Path = null;

    /**
     * Stated class into a phar
     * @var string
     */
    protected $_statedClass3Path = null;

    /**
     * Stated class into a phar
     * @var string
     */
    protected $_statedClass4Path = null;

    /**
     * Stated class into a phar
     * @var string
     */
    protected $_statedClass5Path = null;

    /**
     * Prepare environment before test
     */
    protected function setUp()
    {
        parent::setUp();
        $path = dirname(dirname(dirname(__FILE__))).'/Support/StatedClass/';
        $this->_statedClass1Path = $path.'Class1';
        $this->_statedClass2Path = $path.'Class2';
        $this->_statedClass3Path = $path.'Class3';
        $this->_statedClass4Path = $path.'Class4';
        $this->_statedClass5Path = $path.'Class5';
        chmod($this->_statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0755);
    }

    /**
     * Clean environment after test
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Initialize the finder for test
     * @param string $statedClassName
     * @param string $pathString
     * @return Loader\FinderStandard
     */
    protected function _initializeFind($statedClassName, $pathString)
    {
        $virtualDIContainer = new Support\MockDIContainer();
        $this->_finder = new Loader\FinderStandard($statedClassName, $pathString);
        $this->_finder->setDIContainer($virtualDIContainer);
        return $this->_finder;
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = new Loader\FinderStandard('', '');
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
        $object = new Loader\FinderStandard('', '');
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\MockDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    public function testListStatePathNotFound()
    {
        $this->_initializeFind('virtualStatedClass', '/NonExistentPath');
        try {
            $this->_finder->listStates();
        } catch (Exception\UnavailablePath $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state path does not exist, the Finder must throw an exception `Exception\UnavailablePath`');
    }

    public function testListStatePathNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testListStatePathNotReadable()
    {
        chmod($this->_statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->listStates();
        } catch (Exception\UnReadablePath $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state path is not readable, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    public function testListStatePathNotReadableInPhar()
    {
        $this->markTestSkipped();
    }

    public function testListStates()
    {
        $statesNamesArray = $this->_initializeFind('Class1', $this->_statedClass1Path)->listStates();
        $this->assertInstanceOf('ArrayObject', $statesNamesArray);
        $states = $statesNamesArray->getArrayCopy();
        sort($states);
        $this->assertEquals(
            array(
                'State1',
                'State3',
                'State4',
                'State4b'
            ),
            $states
        );
    }

    public function testListStatesInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadStateNotFound()
    {
        chmod($this->_statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->loadState('State2');
        } catch (Exception\UnReadablePath $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state class path does not exist, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    public function testLoadStateNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadStateWithoutClass()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->loadState('State1');
        } catch (Exception\UnavailableState $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    public function testLoadStateWithoutClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadState()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        $return = $this->_finder->loadState('State4b');
        $this->assertTrue(class_exists('Class1\States\State4b', false));
        $this->assertEquals('Class1\\States\\State4b', $return);
    }

    public function testLoadStatePhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildStateNotFound()
    {
        chmod($this->_statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->buildState('State2');
        } catch (Exception\UnReadablePath $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state class path does not exist, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    public function testBuildStateNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildStateWithoutClass()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->buildState('State1');
        } catch (Exception\UnavailableState $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    public function testBuildStateWithoutClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildStateBadImplementation()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->buildState('State3');
        } catch (Exception\IllegalState $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state file does not implement the interface States\StateInterface, the Finder must throw an exception `Exception\IllegalState`');
    }

    public function testBuildStateBadImplementationInPhat()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildState()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        $stateObject = $this->_finder->buildState('State4');
        $this->assertEquals('Class1\States\State4', get_class($stateObject));
        $this->assertInstanceOf('\UniAlteri\States\States\StateInterface', $stateObject);
    }

    public function testBuildStatePhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadProxyDefault()
    {
        $this->_initializeFind('Class2', $this->_statedClass2Path);
        $proxyClassName = $this->_finder->loadProxy();
        $this->assertEquals('Class2\\Class2', $proxyClassName);
    }

    public function testLoadProxyDefaultInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildProxyDefault()
    {
        $this->_initializeFind('Class2', $this->_statedClass2Path);
        $proxy = $this->_finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2\\Class2', $proxy);
    }

    public function testBuildProxyDefaultInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadProxySpecificBadClass()
    {
        $this->_initializeFind('Class3', $this->_statedClass3Path);
        try {
            $this->_finder->loadProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy class was not found.');
    }

    public function testBuildProxySpecificBadClass()
    {
        $this->_initializeFind('Class3', $this->_statedClass3Path);
        try {
            $this->_finder->buildProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy class was not found.');
    }

    public function testBuildProxySpecificBadClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildProxySpecificBadInterface()
    {
        $this->_initializeFind('Class4', $this->_statedClass4Path);
        try {
            $this->_finder->buildProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy does not implement the proxy interface.');
    }

    public function testBuildProxySpecificBadInterfaceInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadProxySpecific()
    {
        $this->_initializeFind('Class5', $this->_statedClass5Path);
        $this->_finder->setDIContainer(new Support\MockDIContainer());
        $proxyClassName = $this->_finder->loadProxy();
        $this->assertEquals('Class5\\Class5', $proxyClassName);
    }

    public function testLoadProxySpecificInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildProxySpecific()
    {
        $this->_initializeFind('Class5', $this->_statedClass5Path);
        $this->_finder->setDIContainer(new Support\MockDIContainer());
        $proxy = $this->_finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertNotInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class5\\Class5', $proxy);
    }

    public function testBuildProxySpecificInPhar()
    {
        $this->markTestSkipped(); //todo
    }
}