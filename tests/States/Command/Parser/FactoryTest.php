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
namespace Teknoo\Tests\States\Command\Parser;

use Gaufrette\Filesystem;
use Teknoo\States\Command\Parser\Exception\ClassNotFound;
use Teknoo\States\Command\Parser\Exception\UnReadablePath;
use Teknoo\States\Command\Parser\Factory;
use Teknoo\States\Loader\LoaderInterface;

/**
 * Class FactoryTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers Teknoo\States\Command\Parser\Factory
 * @covers Teknoo\States\Command\Parser\AbstractParser
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    protected function buildFileSystemMock()
    {
        if (!$this->fileSystem instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->fileSystem = $this->createMock(
                '\Gaufrette\Filesystem');
        }

        return $this->fileSystem;
    }

    /**
     * Prepare and build a factory parser to test.
     *
     * @param string $path
     *
     * @return Factory
     */
    protected function buildFactoryParser($path)
    {
        return new Factory(
            $this->buildFileSystemMock(),
            $path
        );
    }

    public function testIsValidFactoryBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isValidFactory();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class is not readable');
    }

    public function testIsValidFactoryBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isValidFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsValidFactoryNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/WithoutImpl';

        try {
            $this->buildFactoryParser($path)
                ->isValidFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsValidFactory()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/GoodFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildFactoryParser($path)->isValidFactory());
    }

    public function testIsStandardFactoryBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isStandardFactory();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class is not readable');
    }

    public function testIsStandardFactoryBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isStandardFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsStandardFactoryNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/WithoutImpl';

        try {
            $this->buildFactoryParser($path)
                ->isStandardFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsStandardFactoryNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/GoodFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->isStandardFactory());
    }

    public function testIsStandardFactoryStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/StdFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildFactoryParser($path)->isStandardFactory());
    }

    public function testIsStandardFactoryInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/IntFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->isStandardFactory());
    }

    public function testIsIntegratedFactoryBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isIntegratedFactory();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class is not readable');
    }

    public function testIsIntegratedFactoryBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->isIntegratedFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsIntegratedFactoryNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/WithoutImpl';

        try {
            $this->buildFactoryParser($path)
                ->isIntegratedFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testIsIntegratedFactoryNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/GoodFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->isIntegratedFactory());
    }

    public function testIsIntegratedFactoryStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/StdFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->isIntegratedFactory());
    }

    public function testIsIntegratedFactoryInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/IntFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildFactoryParser($path)->isIntegratedFactory());
    }

    public function testUseTraitFactoryBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->useTraitFactory();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class is not readable');
    }

    public function testUseTraitFactoryBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/BadFactory';

        try {
            $this->buildFactoryParser($path)
                ->useTraitFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testUseTraitFactoryNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/WithoutImpl';

        try {
            $this->buildFactoryParser($path)
                ->useTraitFactory();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the factory class was not found');
    }

    public function testUseTraitFactoryNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/GoodFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->useTraitFactory());
    }

    public function testUseTraitFactoryStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/StdFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->useTraitFactory());
    }

    public function testUseTraitFactoryInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/IntFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildFactoryParser($path)->useTraitFactory());
    }

    public function testUseTraitFactoryTrait()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo(LoaderInterface::FACTORY_FILE_NAME))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Factory/Acme/TraitFactory';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildFactoryParser($path)->useTraitFactory());
    }
}
