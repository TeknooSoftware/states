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

/**
 * Class FinderStandardTest
 * Tests the excepted behavior of standard finder implementing the interface \UniAlteri\States\Loader\FinderInterface
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class FinderStandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Finder to test
     * @var Loader\FinderInterface
     */
    protected $_finder = null;

    /**
     * Mock stated class 1
     * @var string
     */
    protected $_statedClass1Path = null;

    /**
     * Mock stated class 2
     * @var string
     */
    protected $_statedClass2Path = null;

    /**
     * Mock stated class 3
     * @var string
     */
    protected $_statedClass3Path = null;

    /**
     * Mock stated class 4
     * @var string
     */
    protected $_statedClass4Path = null;

    /**
     * Mock stated class 5
     * @var string
     */
    protected $_statedClass5Path = null;

    /**
     * Prepare environment before test
     */
    protected function setUp()
    {
        parent::setUp();
        //Use mock stated class defined un Support directory
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

    /**
     * Test the behavior of the finder when the sub directory where states files are stored does not exist
     */
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

    /**
     * Phar are not currently supported
     */
    public function testListStatePathNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the sub directory where states files are stored is not readable
     */
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

    /**
     * Phar are not currently supported
     */
    public function testListStatePathNotReadableInPhar()
    {
        $this->markTestSkipped();
    }

    /**
     * Test the behavior of the finder when the sub directory is browsed
     */
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

    /**
     * Phar are not currently supported
     */
    public function testListStatesInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the file of the loading state is not readable
     */
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

    /**
     * Phar are not currently supported
     */
    public function testLoadStateNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the state class has not been loaded after including
     */
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

    /**
     * Phar are not currently supported
     */
    public function testLoadStateWithoutClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder to load state
     */
    public function testLoadState()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        $return = $this->_finder->loadState('State4b');
        $this->assertTrue(class_exists('Class1\States\State4b', false));
        $this->assertEquals('Class1\\States\\State4b', $return);
    }

    /**
     * Phar are not currently supported
     */
    public function testLoadStatePhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the file of the building state is not readable
     */
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

    /**
     * Phar are not currently supported
     */
    public function testBuildStateNotFoundInPhar()
    {
        $this->markTestSkipped(); //todo
    }


    /**
     * Test the behavior of the finder when the state class has not been loaded after including
     */
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

    /**
     * Phar are not currently supported
     */
    public function testBuildStateWithoutClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the building state does not implement the interface States\StateInterface
     */
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

    /**
     * Phar are not currently supported
     */
    public function testBuildStateBadImplementationInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder to load (if needed) and build state
     */
    public function testBuildState()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        $stateObject = $this->_finder->buildState('State4');
        $this->assertEquals('Class1\States\State4', get_class($stateObject));
        $this->assertInstanceOf('\UniAlteri\States\States\StateInterface', $stateObject);
    }

    /**
     * Phar are not currently supported
     */
    public function testBuildStatePhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder to create an alias of the Standard Proxy with the stated class name
     * when developers are not defined a proxy for the current stated class
     */
    public function testLoadProxyDefault()
    {
        $this->_initializeFind('Class2', $this->_statedClass2Path);
        $proxyClassName = $this->_finder->loadProxy();
        $this->assertEquals('Class2\\Class2', $proxyClassName);
    }

    /**
     * Phar are not currently supported
     */
    public function testLoadProxyDefaultInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder to create an alias of the Standard Proxy with the stated class name
     * when developers are not defined a proxy for the current stated class and create a new instance of this proxy
     */
    public function testBuildProxyDefault()
    {
        $this->_initializeFind('Class2', $this->_statedClass2Path);
        $proxy = $this->_finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2\\Class2', $proxy);
    }

    /**
     * Phar are not currently supported
     */
    public function testBuildProxyDefaultInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the building proxy has not been found into the proxy file
     */
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

    /**
     * Test the behavior of the finder when the building proxy has not been found into the proxy file
     */
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

    /**
     * Phar are not currently supported
     */
    public function testBuildProxySpecificBadClassInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test the behavior of the finder when the building proxy does not implement the interface Proxy\ProxyInterface
     */
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

    /**
     * Phar are not currently supported
     */
    public function testBuildProxySpecificBadInterfaceInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder load the developer defined proxy into the proxy file in the stated class path
     */
    public function testLoadProxySpecific()
    {
        $this->_initializeFind('Class5', $this->_statedClass5Path);
        $this->_finder->setDIContainer(new Support\MockDIContainer());
        $proxyClassName = $this->_finder->loadProxy();
        $this->assertEquals('Class5\\Class5', $proxyClassName);
    }

    /**
     * Phar are not currently supported
     */
    public function testLoadProxySpecificInPhar()
    {
        $this->markTestSkipped(); //todo
    }

    /**
     * Test normal behavior of the finder load if needed and build the developer defined proxy into
     * the proxy file in the stated class path
     */
    public function testBuildProxySpecific()
    {
        $this->_initializeFind('Class5', $this->_statedClass5Path);
        $this->_finder->setDIContainer(new Support\MockDIContainer());
        $proxy = $this->_finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertNotInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class5\\Class5', $proxy);
    }

    /**
     * Phar are not currently supported
     */
    public function testBuildProxySpecificInPhar()
    {
        $this->markTestSkipped(); //todo
    }
}