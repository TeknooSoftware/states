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
 *
 * @version     1.1.1
 */

namespace UniAlteri\Tests\States\Loader;

use UniAlteri\States\Loader;
use UniAlteri\States\States;
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

/**
 * Class FinderStandardTest
 * Tests the excepted behavior of standard finder implementing the interface \UniAlteri\States\Loader\FinderInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class FinderStandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Finder to test.
     *
     * @var Loader\FinderInterface
     */
    protected $finder = null;

    /**
     * Mock stated class 1.
     *
     * @var string
     */
    protected $statedClass1Path = null;

    /**
     * Mock stated class 2.
     *
     * @var string
     */
    protected $statedClass2Path = null;

    /**
     * Mock stated class 3.
     *
     * @var string
     */
    protected $statedClass3Path = null;

    /**
     * Mock stated class 4.
     *
     * @var string
     */
    protected $statedClass4Path = null;

    /**
     * Mock stated class 5.
     *
     * @var string
     */
    protected $statedClass5Path = null;

    /**
     * Mock stated class 6.
     *
     * @var string
     */
    protected $statedClass6Path = null;

    /**
     * Mock stated class 7.
     *
     * @var string
     */
    protected $statedClass7Path = null;

    /**
     * Mock stated class 8.
     *
     * @var string
     */
    protected $statedClass8Path = null;

    /**
     * Prepare environment before test.
     */
    protected function setUp()
    {
        parent::setUp();
        //Use mock stated class defined un Support directory
        $path = dirname(dirname(dirname(__FILE__))).'/Support/StatedClass/';
        $this->statedClass1Path = $path.'Class1';
        $this->statedClass2Path = $path.'Class2';
        $this->statedClass3Path = $path.'Class3';
        $this->statedClass4Path = $path.'Class4';
        $this->statedClass5Path = $path.'Class5';
        $this->statedClass6Path = $path.'Class6';
        $this->statedClass7Path = $path.'Class7';
        $this->statedClass8Path = $path.'Class8';
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0755);
    }

    /**
     * Clean environment after test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Initialize the finder for test.
     *
     * @param string $statedClassName
     * @param string $pathString
     *
     * @return Loader\FinderStandard
     */
    protected function initializeFinder($statedClassName, $pathString)
    {
        $virtualDIContainer = new Support\MockDIContainer();
        $this->finder = new Loader\FinderStandard($statedClassName, $pathString);
        $this->finder->setDIContainer($virtualDIContainer);

        return $this->finder;
    }

    /**
     * Test behavior for methods Set And GetDiContainer.
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
     * Test the behavior of the finder when the sub directory where states files are stored does not exist.
     */
    public function testListStatePathNotFound()
    {
        $this->initializeFinder('virtualStatedClass', '/NonExistentPath');
        try {
            $this->finder->listStates();
        } catch (Exception\UnavailablePath $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state path does not exist, the Finder must throw an exception `Exception\UnavailablePath`');
    }

    /**
     * Phar are not currently supported.
     */
    public function testListStatePathNotFoundInPhar()
    {
        $this->initializeFinder('virtualStatedClass', 'phar://');
        try {
            $this->finder->listStates();
        } catch (Exception\UnavailablePath $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state path does not exist, the Finder must throw an exception `Exception\UnavailablePath`');
    }

    /**
     * Test the behavior of the finder when the sub directory where states files are stored is not readable.
     */
    public function testListStatePathNotReadable()
    {
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->listStates();
        } catch (Exception\UnReadablePath $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state path is not readable, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    /**
     * Test the behavior of the finder when the sub directory is browsed.
     */
    public function testListStates()
    {
        $statesNamesArray = $this->initializeFinder('Class1', $this->statedClass1Path)->listStates();
        $this->assertInstanceOf('ArrayObject', $statesNamesArray);
        $states = $statesNamesArray->getArrayCopy();
        sort($states);
        $this->assertEquals(
            array(
                'State1',
                'State3',
                'State4',
                'State4b',
            ),
            $states
        );
    }

    /**
     * Test the behavior of the finder when the file of the loading state is not readable.
     */
    public function testLoadStateNotFound()
    {
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->loadState('State2');
        } catch (Exception\UnReadablePath $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state class path does not exist, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    /**
     * Test the behavior of the finder when the state class has not been loaded after including.
     */
    public function testLoadStateWithoutClass()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->loadState('State1');
        } catch (Exception\UnavailableState $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    /**
     * Test normal behavior of the finder to load state.
     */
    public function testLoadState()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        $return = $this->finder->loadState('State4b');
        $this->assertTrue(class_exists('Class1\States\State4b', false));
        $this->assertEquals('Class1\\States\\State4b', $return);
    }

    /**
     * Test the behavior of the finder when the file of the building state is not readable.
     */
    public function testBuildStateNotFound()
    {
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0000);
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->buildState('State2');
        } catch (Exception\UnReadablePath $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state class path does not exist, the Finder must throw an exception `Exception\UnReadablePath`');
    }

    /**
     * Test the behavior of the finder when the state class has not been loaded after including.
     */
    public function testBuildStateWithoutClass()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->buildState('State1');
        } catch (Exception\UnavailableState $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    /**
     * Test the behavior of the finder when the building state does not implement the interface States\StateInterface.
     */
    public function testBuildStateBadImplementation()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->buildState('State3');
        } catch (Exception\IllegalState $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if the state file does not implement the interface States\StateInterface, the Finder must throw an exception `Exception\IllegalState`');
    }

    /**
     * Test normal behavior of the finder to load (if needed) and build state.
     */
    public function testBuildState()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        $stateObject = $this->finder->buildState('State4');
        $this->assertEquals('Class1\States\State4', get_class($stateObject));
        $this->assertInstanceOf('\UniAlteri\States\States\StateInterface', $stateObject);
    }

    /**
     * Test normal behavior of the finder to create an alias of the Standard Proxy with the stated class name
     * when developers are not defined a proxy for the current stated class.
     */
    public function testLoadProxyDefault()
    {
        $this->initializeFinder('Class2', $this->statedClass2Path);
        $proxyClassName = $this->finder->loadProxy();
        $this->assertEquals('Class2\\Class2', $proxyClassName);
    }

    /**
     * Test normal behavior of the finder to create an alias of the Standard Proxy with the stated class name
     * when developers are not defined a proxy for the current stated class and create a new instance of this proxy.
     */
    public function testBuildProxyDefault()
    {
        $this->initializeFinder('Class2', $this->statedClass2Path);
        $proxy = $this->finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2\\Class2', $proxy);
    }

    /**
     * Test the behavior of the finder when the building proxy has not been found into the proxy file.
     */
    public function testLoadProxySpecificBadClass()
    {
        $this->initializeFinder('Class3', $this->statedClass3Path);
        try {
            $this->finder->loadProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy class was not found.');
    }

    /**
     * Test the behavior of the finder when the building proxy has not been found into the proxy file.
     */
    public function testBuildProxySpecificBadClass()
    {
        $this->initializeFinder('Class3', $this->statedClass3Path);
        try {
            $this->finder->buildProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy class was not found.');
    }

    /**
     * Test the behavior of the finder when the building proxy does not implement the interface Proxy\ProxyInterface.
     */
    public function testBuildProxySpecificBadInterface()
    {
        $this->initializeFinder('Class4', $this->statedClass4Path);
        try {
            $this->finder->buildProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy does not implement the proxy interface.');
    }

    /**
     * Test normal behavior of the finder load the developer defined proxy into the proxy file in the stated class path.
     */
    public function testLoadProxySpecific()
    {
        $this->initializeFinder('Class5', $this->statedClass5Path);
        $this->finder->setDIContainer(new Support\MockDIContainer());
        $proxyClassName = $this->finder->loadProxy();
        $this->assertEquals('Class5\\Class5', $proxyClassName);
    }

    /**
     * Test normal behavior of the finder load if needed and build the developer defined proxy into
     * the proxy file in the stated class path.
     */
    public function testBuildProxySpecific()
    {
        $this->initializeFinder('Class5', $this->statedClass5Path);
        $this->finder->setDIContainer(new Support\MockDIContainer());
        $proxy = $this->finder->buildProxy();
        $this->assertInstanceOf('\UniAlteri\States\Proxy\ProxyInterface', $proxy);
        $this->assertNotInstanceOf('\UniAlteri\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class5\\Class5', $proxy);
    }

    /**
     * Test if the finder is able to return the stated class name where it's user.
     */
    public function testGetStatedClassName()
    {
        $this->initializeFinder('It\A\Stated\Class', $this->statedClass5Path);
        $this->assertEquals('It\A\Stated\Class', $this->finder->getStatedClassName());
    }

    /**
     * Test behavior of finder method listParentsClassesNames when proxy is not loaded.
     */
    public function testListParentsClassesNamesNotFound()
    {
        $this->initializeFinder('UniAlteri\Tests\Support\StatedClass\ClassMissed', $this->statedClass6Path);
        try {
            $this->finder->listParentsClassesNames();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, if the proxy has not been initialized, the finder must  thrown an exception');
    }

    /**
     * Test behavior of finder method listParentsClassesNames when the stated class has a no parent.
     */
    public function testListParentsClassesNamesNoParent()
    {
        $this->initializeFinder('UniAlteri\Tests\Support\StatedClass\Class6', $this->statedClass6Path);
        $this->finder->loadProxy();
        $this->assertEmpty($this->finder->listParentsClassesNames());
    }

    /**
     * Test behavior of finder method listParentsClassesNames when the stated class is a child of another stated class.
     */
    public function testListParentsClassesNamesOneParent()
    {
        $this->initializeFinder('UniAlteri\Tests\Support\StatedClass\Class7', $this->statedClass7Path);
        $this->finder->loadProxy();
        $this->assertEquals(
            ['UniAlteri\Tests\Support\StatedClass\Class6'],
            $this->finder->listParentsClassesNames()->getArrayCopy()
        );
    }

    /**
     * Test behavior of finder method listParentsClassesNames when the stated class is a grand child class.
     */
    public function testListParentsClassesNamesMultiParent()
    {
        $this->initializeFinder('UniAlteri\Tests\Support\StatedClass\Class8', $this->statedClass8Path);
        $this->finder->loadProxy();
        $this->assertEquals(
            [
                'UniAlteri\Tests\Support\StatedClass\Class7',
                'UniAlteri\Tests\Support\StatedClass\Class6',
            ],
            $this->finder->listParentsClassesNames()->getArrayCopy()
        );
    }
}
