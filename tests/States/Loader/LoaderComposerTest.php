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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\Loader;

use Composer\Autoload\ClassLoader;
use UniAlteri\States\Loader;
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

/**
 * Class LoaderComposerTest
 * Tests the excepted behavior of standard loader implementing the interface \UniAlteri\States\Loader\LoaderInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @covers UniAlteri\States\Loader\LoaderComposer
 */
class LoaderComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Loader to test.
     *
     * @var Loader\LoaderInterface
     */
    protected $loader = null;

    /**
     * Where files to generate the phar file for test are located.
     *
     * @var string
     */
    protected $srcPharPath = null;

    /**
     * Where the phar file for test is located.
     *
     * @var string
     */
    protected $pharFileNamespace = null;

    /**
     * @var ClassLoader;
     */
    protected $classLoaderMock;

    /**
     * Prepare environment before test.
     */
    protected function setUp()
    {
        //Build phar archives
        $this->srcPharPath = dirname(dirname(dirname(__FILE__))).'/Support/src/';
        //namespace
        $this->pharFileNamespace = dirname(dirname(dirname(__FILE__))).'/Support/pharFileNamespace.phar';

        if (0 == ini_get('phar.readonly') && !file_exists($this->pharFileNamespace)) {
            $phar = new \Phar($this->pharFileNamespace, 0, 'pharFileNamespace.phar');
            $phar->buildFromDirectory($this->srcPharPath.'/NamespaceLoader/');
        }

        parent::setUp();
    }

    /**
     * Clean environment after test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test if this suit test can run test on Phar.
     *
     * @return bool
     */
    protected function pharTestsAreAvailable()
    {
        return (class_exists('\Phar', false) && file_exists($this->pharFileNamespace));
    }

    /**
     * @return ClassLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClassLoaderMock()
    {
        if (!$this->classLoaderMock instanceof ClassLoader) {
            $this->classLoaderMock = $this->getMock('Composer\Autoload\ClassLoader', [], [], '', false);
        }

        return $this->classLoaderMock;
    }

    /**
     * Load object to test it.
     *
     * @return Loader\LoaderComposer
     */
    protected function initializeLoader()
    {
        $this->loader = new Loader\LoaderComposer(
            $this->getClassLoaderMock(),
            function () {
                return new Support\MockFinder('my\Stated\Class', 'my\Path');
            },
            new \ArrayObject([])
        );

        return $this->loader;
    }

    /**
     * Developers can register several namespace with several locations into the loader to accelerate the loading process
     * If the location is invalid, loader must throws exception.
     *
     * @expectedException \TypeError
     */
    public function testRegisterNamespaceBadName()
    {
        $loader = $this->initializeLoader();
        $loader->registerNamespace('badNamespace', array());
    }

    /**
     * Test behavior of the loader when it cannot found and load the required class : it must return false
     * and give the hand to another loader.
     */
    public function testLoadClassNonExistent()
    {
        $this->assertFalse($this->initializeLoader()->loadClass('badName'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found, the loader must throw an exception.
     */
    public function testBuildFactoryNonExistentFactory()
    {
        $loader = $this->initializeLoader();
        try {
            $loader->buildFactory('badFactory', 'statedClassName', 'path');
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if factory\'s class was not found, Loader must throws the exception Exception\UnavailableFactory');
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory class does not implement the method Factory\FactoryInterface, the loader must throw an exception.
     */
    public function testBuildFactoryBadFactory()
    {
        $loader = $this->initializeLoader();
        try {
            $loader->buildFactory('stdClass', 'statedClassName', 'path');
        } catch (Exception\IllegalFactory $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, if factory\'s class does not implement the factory interface, Loader must throws the exception Exception\IllegalFactory');
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     */
    public function testBuildFactory()
    {
        Support\MockFactory::resetInitializedFactories();
        $loader = $this->initializeLoader();
        $this->assertEquals(array(), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class1', 'path1');
        $this->assertEquals(array('class1'), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class2', 'path2');
        $this->assertEquals(array('class1', 'class2'), Support\MockFactory::listInitializedFactories());
        $loader->buildFactory('\\UniAlteri\\Tests\\Support\\MockFactory', 'class1', 'path3');
        $this->assertEquals(
            array('class1', 'class2', 'class1'),
            Support\MockFactory::listInitializedFactories(),
            'Error, the loader must not manage factory building. If an even stated class is initialized several times, the loader must call the factory each time. '
        );
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceRelativeWithoutFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return false;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceRelativeWithoutFactoryFileMultiple()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->exactly(2))
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return false;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceWithProxyRelativeWithoutFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceRelativeWithEmptyFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Class1b':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1b'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceWithProxyRelativeWithEmptyFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Class1b\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Class1b':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1b\\Class1b'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceRelative()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceRelativeMultiple()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->atMost(2))
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceWithProxyRelative()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsoluteWithoutFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsoluteWithoutFactoryFileMultiple()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->exactly(2))
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsoluteWithProxyWithoutFactoryFile()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1\\Class1'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsolute()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsoluteMultiples()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->atMost(2))
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassViaNameSpaceAbsoluteWithProxy()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Factory':
                        include_once $path.'/Class2/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2':
                        include_once $path.'/Class2/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2\\Class2'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     *
     * @expectedException \Exception
     */
    public function testLoadClassViaNameSpaceAbsoluteWithFactoryException()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Class3\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Factory':
                        include_once $path.'/Class3/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Class3':
                        include_once $path.'/Class3/Class3.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class3'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     *
     * @expectedException \Exception
     */
    public function testLoadClassViaNameSpaceWithProxyAbsoluteWithFactoryException()
    {
        $loader = $this->initializeLoader();
        $path = dirname(dirname(__DIR__)).'/Support/NamespaceLoader/';

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Class3\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Factory':
                        include_once $path.'/Class3/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Class3':
                        include_once $path.'/Class3/Class3.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', $path);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class3\\Class3'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceRelativeWithoutFactoryFile()
    {
        if (!$this->pharTestsAreAvailable()) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceWithProxyRelativeWithoutFactoryFile()
    {
        if (!$this->pharTestsAreAvailable()) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceRelativeWithEmptyFactoryFile()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceWithProxyRelativeWithEmptyFactoryFile()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1bPhar\\Class1bPhar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceRelative()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Factory':
                        include_once $path.'/Class2Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar':
                        include_once $path.'/Class2Phar/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceWithProxyRelative()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Factory':
                        include_once $path.'/Class2Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar':
                        include_once $path.'/Class2Phar/Class2.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceAbsoluteWithoutFactoryFile()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class2Phar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceAbsoluteWithProxyWithoutFactoryFile()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar':
                        return false;
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class1Phar\\Class1Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceAbsolute()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Factory':
                        include_once $path.'/Class2Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar':
                        include_once $path.'/Class2Phar/Class2Phar.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     */
    public function testLoadClassInPharViaNameSpaceAbsoluteWithProxy()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Factory':
                        include_once $path.'/Class2Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar':
                        include_once $path.'/Class2Phar/Class2Phar.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertTrue($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class2Phar\\Class2Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     *
     * @expectedException \Exception
     */
    public function testLoadClassInPharViaNameSpaceAbsoluteWithFactoryException()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Class3Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Factory':
                        include_once $path.'/Class3Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Class3Phar':
                        include_once $path.'/Class3Phar/Class3Phar.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar'));
    }

    /**
     * After found the stated class, the loader must load its factory and initialize it by calling its initialize() method.
     * If the factory was not found (file not present, class not in the file, or exception during factory loading)
     * the loader must ignore the stated class and return false.
     *
     * @expectedException \Exception
     */
    public function testLoadClassInPharViaNameSpaceWithProxyAbsoluteWithFactoryException()
    {
        if (!class_exists('\Phar', false)) {
            $this->markTestSkipped('Phar extension is not available');

            return;
        }

        $loader = $this->initializeLoader();
        $path = 'phar://'.$this->pharFileNamespace;

        $this->getClassLoaderMock()->expects($this->any())
            ->method('addPsr4')
            ->with(
                $this->equalTo('UniAlteri\\Tests\\Support\\Loader\\'),
                $this->equalTo($path)
            );

        $this->getClassLoaderMock()->expects($this->any())
            ->method('loadClass')
            ->willReturnCallback(function ($className) use ($path) {
                switch ($className) {
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Class3Phar\\Factory':
                        return false;
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Factory':
                        include_once $path.'/Class3Phar/Factory.php';
                        break;
                    case '\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Class3Phar':
                        include_once $path.'/Class3Phar/Class3Phar.php';
                        break;
                    default:
                        $this->fail('Unknown class loading '.$className);
                        break;
                }

                return true;
            });

        $loader->registerNamespace('\\UniAlteri\\Tests\\Support\\Loader', 'phar://'.$this->pharFileNamespace);
        $this->assertFalse($loader->loadClass('\\UniAlteri\\Tests\\Support\\Loader\\Class3Phar\\Class3Phar'));
    }
}
