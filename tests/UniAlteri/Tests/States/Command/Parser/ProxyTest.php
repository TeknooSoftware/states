<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.1
 */
namespace UniAlteri\Tests\States\Command\Parser;

use UniAlteri\States\Command\Parser\Proxy;
use Gaufrette\Filesystem;
use UniAlteri\States\Exception\UnReadablePath;
use UniAlteri\States\Exception\ClassNotFound;

/**
 * Class ProxyTest
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ProxyTest extends \PHPUnit_Framework_TestCase
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
            $this->fileSystem = $this->getMock(
                '\Gaufrette\Filesystem',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->fileSystem;
    }

    /**
     * Prepare and build a proxy parser to test
     * @param  string $path
     * @return Proxy
     */
    protected function buildProxyParser($path)
    {
        return new Proxy(
            $this->buildFileSystemMock(),
            $path
        );
    }

    public function testIsValidProxyBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isValidProxy();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testIsValidProxyBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isValidProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsValidProxyNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/WithoutImpl';

        try {
            $this->buildProxyParser($path)
                ->isValidProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsValidProxy()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('GoodProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/GoodProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildProxyParser($path)->isValidProxy());
    }

    public function testIsStandardProxyBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isStandardProxy();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testIsStandardProxyBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isStandardProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsStandardProxyNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/WithoutImpl';

        try {
            $this->buildProxyParser($path)
                ->isStandardProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsStandardProxyNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('GoodProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/GoodProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->isStandardProxy());
    }

    public function testIsStandardProxyStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('StdProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/StdProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildProxyParser($path)->isStandardProxy());
    }

    public function testIsStandardProxyInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('IntProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/IntProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->isStandardProxy());
    }

    public function testIsIntegratedProxyBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isIntegratedProxy();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testIsIntegratedProxyBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->isIntegratedProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsIntegratedProxyNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/WithoutImpl';

        try {
            $this->buildProxyParser($path)
                ->isIntegratedProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsIntegratedProxyNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('GoodProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/GoodProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->isIntegratedProxy());
    }

    public function testIsIntegratedProxyStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('StdProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/StdProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->isIntegratedProxy());
    }

    public function testIsIntegratedProxyInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('IntProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/IntProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildProxyParser($path)->isIntegratedProxy());
    }

    public function testUseTraitProxyBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->useTraitProxy();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testUseTraitProxyBadFile2()
    {
        $counter = 0;
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return true;
                    } else {
                        return false;
                    }
                }
            );

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->useTraitProxy();
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testUseTraitProxyBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('BadProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/BadProxy';

        try {
            $this->buildProxyParser($path)
                ->useTraitProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testUseTraitProxyNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/WithoutImpl';

        try {
            $this->buildProxyParser($path)
                ->useTraitProxy();
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testUseTraitProxyNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('GoodProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/GoodProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->useTraitProxy());
    }

    public function testUseTraitProxyStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('StdProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/StdProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->useTraitProxy());
    }

    public function testUseTraitProxyInt()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('IntProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/IntProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildProxyParser($path)->useTraitProxy());
    }

    public function testUseTraitProxyTrait()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('TraitProxy.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/Proxy/Acme/TraitProxy';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function ($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildProxyParser($path)->useTraitProxy());
    }
}
