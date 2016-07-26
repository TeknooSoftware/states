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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\States\Loader;

use Composer\Autoload\ClassLoader;
use Teknoo\States\Loader;
use Teknoo\States\State;
use Teknoo\States\Loader\Exception;
use Teknoo\Tests\Support;

/**
 * Class FinderComposerTest
 * Tests the excepted behavior of standard finder implementing the interface \Teknoo\States\Loader\FinderInterface.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers Teknoo\States\Loader\FinderComposer
 */
class FinderComposerTest extends \PHPUnit_Framework_TestCase
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
     * @var ClassLoader;
     */
    protected $classLoaderMock;

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
        $this->classLoaderMock = null;
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0755);
    }

    /**
     * Clean environment after test.
     */
    protected function tearDown()
    {
        chmod($this->statedClass1Path.DIRECTORY_SEPARATOR.Loader\FinderInterface::STATES_PATH, 0755);
    }

    /**
     * @return ClassLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClassLoaderMock()
    {
        if (!$this->classLoaderMock instanceof ClassLoader) {
            $this->classLoaderMock = $this->createMock('Composer\Autoload\ClassLoader');
        }

        return $this->classLoaderMock;
    }

    /**
     * Initialize the finder for test.
     *
     * @param string $statedClassName
     * @param string $pathString
     *
     * @return Loader\FinderComposer
     */
    protected function initializeFinder($statedClassName, $pathString)
    {
        $this->finder = new Loader\FinderComposer($statedClassName, $pathString, $this->getClassLoaderMock());

        return $this->finder;
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
            $this->fail($e->getMessage());
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
            $this->fail($e->getMessage());
        }

        $this->fail('Error, if the state path does not exist, the Finder must throw an exception `Exception\UnavailablePath`');
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
                'State5',
                'State6',
            ),
            $states
        );
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
            $this->fail($e->getMessage());
        }

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    /**
     * Test normal behavior of the finder to load state.
     */
    public function testLoadState()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class1\States\State4b'))
            ->willReturnCallback(function () {
                include_once $this->statedClass1Path.'/States/State4b.php';

                return true;
            });

        $return = $this->finder->loadState('State4b');
        $this->assertTrue(class_exists('Class1\\States\\State4b', false));
        $this->assertEquals('Class1\\States\\State4b', $return);
    }

    /**
     * Test the behavior of the finder when the state class has not been loaded after including.
     */
    public function testBuildStateWithoutClass()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);
        try {
            $this->finder->buildState('State1', false, 'Class1');
        } catch (Exception\UnavailableState $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, if the state file does not contain the required class, the Finder must throw an exception `Exception\UnavailableState`');
    }

    /**
     * Test the behavior of the finder when the building state does not implement the interface States\StateInterface.
     */
    public function testBuildStateBadImplementation()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class1\States\State3'))
            ->willReturnCallback(function () {
                include_once $this->statedClass1Path.'/States/State3.php';

                return true;
            });

        try {
            $this->finder->buildState('State3', false, 'Class1');
        } catch (Exception\IllegalState $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, if the state file does not implement the interface States\StateInterface, the Finder must throw an exception `Exception\IllegalState`');
    }

    /**
     * Test normal behavior of the finder to load (if needed) and build state.
     */
    public function testBuildState()
    {
        $this->initializeFinder('Class1', $this->statedClass1Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class1\States\State4'))
            ->willReturnCallback(function () {
                include_once $this->statedClass1Path.'/States/State4.php';

                return true;
            });

        $stateObject = $this->finder->buildState('State4', false, 'Class1');
        $this->assertEquals('Class1\States\State4', get_class($stateObject));
        $this->assertInstanceOf('\Teknoo\States\State\StateInterface', $stateObject);
    }

    /**
     * Test normal behavior of the finder to create an alias of the Standard Proxy with the stated class name
     * when developers are not defined a proxy for the current stated class.
     */
    public function testLoadProxyDefault()
    {
        $this->initializeFinder('Class2', $this->statedClass2Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class2\\Class2'))
            ->willReturnCallback(function () {
                return false;
            });

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
        $this->assertInstanceOf('\Teknoo\States\Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('\Teknoo\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2\\Class2', $proxy);
    }

    /**
     * Test the behavior of the finder when the building proxy does not implement the interface Proxy\ProxyInterface.
     */
    public function testBuildProxySpecificBadInterface()
    {
        $this->initializeFinder('Class4', $this->statedClass4Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class4\Class4'))
            ->willReturnCallback(function () {
                include_once $this->statedClass4Path.'/Class4.php';

                return true;
            });

        try {
            $this->finder->buildProxy();
        } catch (Exception\IllegalProxy $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('Error, the finder must throw an exception Exception\IllegalProxy when the proxy does not implement the proxy interface.');
    }

    /**
     * Test normal behavior of the finder load the developer defined proxy into the proxy file in the stated class path.
     */
    public function testLoadProxySpecific()
    {
        $this->initializeFinder('Class5', $this->statedClass5Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class5\Class5'))
            ->willReturnCallback(function () {
                include_once $this->statedClass5Path.'/Class5.php';

                return true;
            });

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

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Class5\Class5'))
            ->willReturnCallback(function () {
                include_once $this->statedClass5Path.'/Class5.php';

                return true;
            });

        $proxy = $this->finder->buildProxy();
        $this->assertInstanceOf('\Teknoo\States\Proxy\ProxyInterface', $proxy);
        $this->assertNotInstanceOf('\Teknoo\States\Proxy\Standard', $proxy);
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
        $this->initializeFinder('Teknoo\Tests\Support\StatedClass\ClassMissed', $this->statedClass6Path);
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
        $this->initializeFinder('Teknoo\Tests\Support\StatedClass\Class6', $this->statedClass6Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) {
                include_once $this->statedClass6Path.'/Class6.php';

                return true;
            });

        $this->finder->loadProxy();
        $this->assertEmpty($this->finder->listParentsClassesNames());
    }

    /**
     * Test behavior of finder method listParentsClassesNames when the stated class is a child of another stated class.
     */
    public function testListParentsClassesNamesOneParent()
    {
        $this->initializeFinder('Teknoo\Tests\Support\StatedClass\Class7', $this->statedClass7Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) {
                include_once $this->statedClass6Path.'/Class6.php';
                include_once $this->statedClass7Path.'/Class7.php';

                return true;
            });

        $this->finder->loadProxy();
        $this->assertEquals(
            ['Teknoo\Tests\Support\StatedClass\Class6'],
            $this->finder->listParentsClassesNames()->getArrayCopy()
        );
    }

    /**
     * Test behavior of finder method listParentsClassesNames when the stated class is a grand child class.
     */
    public function testListParentsClassesNamesMultiParent()
    {
        $this->initializeFinder('Teknoo\Tests\Support\StatedClass\Class8', $this->statedClass8Path);

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->with($this->equalTo('Teknoo\Tests\Support\StatedClass\Class8\Class8'))
            ->willReturnCallback(function () {
                include_once $this->statedClass6Path.'/Class6.php';
                include_once $this->statedClass7Path.'/Class7.php';
                include_once $this->statedClass8Path.'/Class8.php';

                return true;
            });

        $this->finder->loadProxy();
        $this->assertEquals(
            [
                'Teknoo\Tests\Support\StatedClass\Class7',
                'Teknoo\Tests\Support\StatedClass\Class6',
            ],
            $this->finder->listParentsClassesNames()->getArrayCopy()
        );
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
     * Check behavior of getStateParentsClassesNamesList() if the finder return all parent class of a state (including external classes).
     */
    public function testGetStateParentsClassesNamesList()
    {
        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($class) {
                $parts = explode('\\', $class);
                $state = array_pop($parts);

                include_once $this->statedClass1Path.'/States/'.$state.'.php';

                return true;
            });

        $this->initializeFinder('Class1', $this->statedClass1Path);
        $this->assertEquals(
            array('Teknoo\Tests\Support\MockState'),
            $this->finder->getStateParentsClassesNamesList('State4b')
        );
        $this->assertEquals(
            array('Class1\States\State4b', 'Teknoo\Tests\Support\MockState'),
            $this->finder->getStateParentsClassesNamesList('State5')
        );
        $this->assertEquals(
            array('Class1\States\State5', 'Class1\States\State4b', 'Teknoo\Tests\Support\MockState'),
            $this->finder->getStateParentsClassesNamesList('State6')
        );
    }
}
