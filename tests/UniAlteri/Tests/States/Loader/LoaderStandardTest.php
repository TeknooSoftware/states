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
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

class LoaderStandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader\LoaderInterface
     */
    protected $_loader = null;

    /**
     * @var Support\MockIncludePathManager
     */
    protected $_includePathManager = null;

    /**
     * Prepare environment before test
     */
    protected function setUp()
    {
        $this->_includePathManager = new Support\MockIncludePathManager();
        parent::setUp();
    }

    /**
     * Clean environment after test
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Load object to test it
     * @param boolean $standardIncludePathManager to load the standard Include Path Manager from this lib and not
     *                  the test manager
     * @return Loader\LoaderStandard
     */
    protected function _initializeLoader($standardIncludePathManager=false)
    {
        if (false == $standardIncludePathManager) {
            $this->_loader = new Loader\LoaderStandard($this->_includePathManager);
        } else {
            $this->_loader = new Loader\LoaderStandard(new Loader\IncludePathManager());
        }

        $this->_loader->setDIContainer(new Support\MockDIContainer());
        $this->_loader->getDIContainer()->registerService(
            Loader\FinderInterface::DI_FINDER_SERVICE,
            function(){
                return new Support\MockFinder('', '');
            }
        );
        return $this->_loader;
    }

    /**
     * the loader must throw an exception Exception\IllegalArgument if the IncludePathManager does not implement the interface IncludePathManagerInterface
     */
    public function testConstructWithBadManager()
    {
        try {
            new Loader\LoaderStandard(new \stdClass());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {}

        $this->fail('Error, the loader must throw an exception Exception\IllegalArgument if the IncludePathManager does not implement the interface IncludePathManagerInterface');
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = new Loader\LoaderStandard($this->_includePathManager);
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
        $object = new Loader\LoaderStandard($this->_includePathManager);
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\MockDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    public function testLoadClassNonExistent()
    {
        $this->assertFalse($this->_initializeLoader()->loadClass('badName'));
    }

    public function testAddIncludePathBadDir()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->addIncludePath('badPath');
        } catch (Exception\UnavailablePath $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if the path to include is unavailable, the loader must throws the exception Exception\UnavailablePath');
    }

    public function testAddIncludePath()
    {
        $loader = $this->_initializeLoader();
        $loader->addIncludePath(__DIR__);
        $loader->addIncludePath(dirname(__DIR__));
        $this->assertEquals(
            array(
                __DIR__,
                dirname(__DIR__)
            ),
            array_values($loader->getIncludedPaths()->getArrayCopy())
        );
    }

    public function testRegisterNamespaceBadName()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->registerNamespace('badNamespace', array());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if the path of namespace to register is not a valid string, the loader must throws the exception Exception\UnavailablePath');
    }

    public function testRegisterNamespace()
    {
        $loader = $this->_initializeLoader();
        $loader->registerNamespace('namespace1', 'path1');
        $loader->registerNamespace('namespace2', 'path2');
        $this->assertEquals(
            array(
                '\\namespace1'    => new \SplQueue(array('path1')),
                '\\namespace2'    => new \SplQueue(array('path2')),
            ),
            $loader->listNamespaces()->getArrayCopy()
        );
    }

    public function testRegisterNamespaceMultiplePath()
    {
        $loader = $this->_initializeLoader();
        $loader->registerNamespace('namespace2', 'path2');
        $loader->registerNamespace('namespace1', 'path1');
        $loader->registerNamespace('namespace1', 'path3');
        $this->assertEquals(
            array(
                '\\namespace1'    => new \SplQueue(array('path1', 'path3')),
                '\\namespace2'    => new \SplQueue(array('path2')),
            ),
            $loader->listNamespaces()->getArrayCopy()
        );
    }

    public function testLoadClassRestoreOldIncludedPathAfterCalling()
    {
        $this->_includePathManager->resetAllChangePath();
        $this->_includePathManager->setIncludePath(
            array(
                __DIR__
            )
        );
        $loader = $this->_initializeLoader();
        $loader->addIncludePath(dirname(__DIR__));
        $loader->loadClass('fakeClass');
        $this->assertEquals(
            array(
                __DIR__
            ),
            $this->_includePathManager->getIncludePath()
        );

        $this->assertEquals(
            array(
                array(
                    __DIR__
                ),
                array(
                    __DIR__,
                    dirname(__DIR__)
                ),
                array(
                    __DIR__
                )
            ),
            $this->_includePathManager->getAllChangePaths()
        );
    }

    public function testBuildFactoryNonExistentFactory()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->buildFactory('badFactory', 'statedClassName', 'path');
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if factory\'s class was not found, Loader must throws the exception Exception\UnavailableFactory');
    }

    public function testBuildFactoryBadFactory()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->buildFactory('stdClass', 'statedClassName', 'path');
        } catch (Exception\IllegalFactory $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if factory\'s class does not implement the factory interface, Loader must throws the exception Exception\IllegalFactory');
    }

    public function testBuildFactory()
    {
        $loader = $this->_initializeLoader();
        $this->assertEquals(array(), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class1', 'path1');
        $this->assertEquals(array('class1:path1'), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class2', 'path2');
        $this->assertEquals(array('class1:path1', 'class2:path2'), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class1', 'path3');
        $this->assertEquals(
            array('class1:path1', 'class2:path2', 'class1:path3'),
            Support\MockFactory::listInitializedFactories(),
            'Error, the loader must not manage factory building. If a even stated class is initialized several times, the loader must call the factory each time. '
        );
    }

    public function testLoadClassViaNameSpaceRelativeWithoutFactoryFile()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    public function testLoadClassViaNameSpaceRelativeWithEmptyFactoryFile()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1b'));
    }

    public function testLoadClassViaNameSpaceRelative()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    public function testLoadClassViaNameSpaceAbsoluteWithoutFactoryFile()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    public function testLoadClassViaNameSpaceAbsolute()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    public function testLoadClassViaNameSpaceAbsoluteWithFactoryException()
    {
        $loader = $this->_initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';
        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class3'));
    }

    public function testLoadClassViaFileWithoutFileAbsolute()
    {
        $loader = $this->_initializeLoader(true);
        $path = dirname(dirname(__DIR__));
        $loader->addIncludePath($path);
        $this->assertFalse($loader->loadClass('\\Support\\FileLoader\\Class1'));
    }

    public function testLoadClassViaFileAbsolute()
    {
        $loader = $this->_initializeLoader(true);
        $path = dirname(dirname(__DIR__));
        $loader->addIncludePath($path);
        $this->assertTrue($loader->loadClass('\\Support\\FileLoader\\Class2'));
    }

    public function testLoadClassViaFileWithoutFile()
    {
        $loader = $this->_initializeLoader(true);
        $path = dirname(dirname(__DIR__));
        $loader->addIncludePath($path);
        $this->assertFalse($loader->loadClass('Support\\FileLoader\\Class1'));
    }

    public function testLoadClassViaFile()
    {
        $loader = $this->_initializeLoader(true);
        $path = dirname(dirname(__DIR__));
        $loader->addIncludePath($path);
        $this->assertTrue($loader->loadClass('Support\\FileLoader\\Class2'));
    }

    public function testLoadClassViaFileWithFactoryException()
    {
        $loader = $this->_initializeLoader(true);
        $path = dirname(dirname(__DIR__));
        $loader->addIncludePath($path);
        $this->assertFalse($loader->loadClass('Support\\FileLoader\\Class3'));
    }

    public function testLoadClassBehaviorDuringExceptionMustRestoreIncludedPath()
    {
        $loader = $this->_initializeLoader(false);
        $path = dirname(dirname(dirname(dirname(__DIR__))));
        $loader->addIncludePath($path);

        $fail = false;
        try {
            $loader->loadClass('UniAlteri\\Tests\\Support\\FileLoader\\Class3b');
        } catch (\Exception $e) {
            $fail = true;
        }

        $this->assertTrue($fail, 'Error, the loader must rethrow exception during loading class');
    }
}