<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 27/05/13
 * Time: 16:25
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
        chmod($this->_statedClass1Path, 7,2, 2);
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
        $virtualDIContainer = new Support\VirtualDIContainer();
        $this->_finder = new Loader\FinderStandard($statedClassName, $pathString);
        return $this->_finder;
    }

    public function testListStatePathNotFound()
    {
        $this->_initializeFind('virtualStatedClass', '/NonExistantPath');
        try {
            $this->_finder->listStates();
        } catch (Exception\UnavailablePath $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state path does not exist, the Finder must throw an exception `Exception\UnavailablePath`');
    }

    public function testListStatePathNotFoundInPhar()
    {
        $this->fail();
    }

    public function testListStatePathNotReadable()
    {
        chmod($this->_statedClass1Path, 000);
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
        $this->fail();
    }

    public function testListStates()
    {
        $statesNamesArray = $this->_initializeFind('Class1', $this->_statedClass1Path)->listStates();
        $this->assertInstanceOf('ArrayObject', $statesNamesArray);
        $this->assertEquals(
            array(
                'State1',
                'State3',
                'State4'
            ),
            $statesNamesArray->getArrayCopy()
        );
    }

    public function testListStatesInPhar()
    {
        $this->fail();
    }

    public function testLoadStateNotFound()
    {
        chmod($this->_statedClass1Path, 000);
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
        $this->fail();
    }

    public function testLoadStateWithoutClass()
    {
        chmod($this->_statedClass1Path, 000);
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
        $this->fail();
    }

    public function testLoadStateBadImplementation()
    {
        chmod($this->_statedClass1Path, 000);
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        try {
            $this->_finder->loadState('State3');
        } catch (Exception\IllegalState $e) {
            return ;
        } catch (\Exception $e) {}

        $this->fail('Error, if the state file does not implement the interface States\StateInterface, the Finder must throw an exception `Exception\IllegalState`');
    }

    public function testLoadStateBadImplementationInPhat()
    {
        $this->fail();
    }

    public function testLoadState()
    {
        $this->_initializeFind('Class1', $this->_statedClass1Path);
        $stateObject = $this->_finder->loadState('State4');
        $this->assertEquals('State4', get_class($stateObject));
        $this->assertInstanceOf('States\StateInterface', $stateObject);
    }

    public function testLoadStatePhat()
    {
        $this->fail();
    }

    public function testLoadProxyDefault()
    {
        $this->_initializeFind('Class2', $this->_statedClass2Path);
        $proxy = $this->_finder->loadProxy();
        $this->assertInstanceOf('Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2'.Loader\FinderInterface::PROXY_SUFFIX_CLASS_NAME, $proxy);
    }

    public function testLoadProxyDefaultInPhar()
    {
        $this->fail();
    }

    public function testLoadProxySpecificBadClass()
    {
        $this->_initializeFind('Class3', $this->_statedClass3Path);
        try {
            $proxy = $this->_finder->loadProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy class was not found.');
    }

    public function testLoadProxySpecificBadClassInPhar()
    {
        $this->fail();
    }

    public function testLoadProxySpecificBadInterface()
    {
        $this->_initializeFind('Class4', $this->_statedClass4Path);
        try {
            $proxy = $this->_finder->loadProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy does not implement the proxy interface.');
    }

    public function testLoadProxySpecificBadInterfaceInPhar()
    {
        $this->fail();
    }

    public function testLoadProxySpecific()
    {
        $this->_initializeFind('Class5', $this->_statedClass5Path);
        $proxy = $this->_finder->loadProxy();
        $this->assertInstanceOf('Proxy\ProxyInterface', $proxy);
        $this->assertNotInstanceOf('Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class5'.Loader\FinderInterface::PROXY_SUFFIX_CLASS_NAME, $proxy);
    }

    public function testLoadProxySpecificInPhar()
    {
        $this->fail();
    }
}