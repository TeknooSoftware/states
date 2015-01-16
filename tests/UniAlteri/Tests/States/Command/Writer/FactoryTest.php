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
 * @version     0.9.9
 */

namespace UniAlteri\Tests\States\Command\Writer;

use Gaufrette\Filesystem;
use UniAlteri\States\Command\Writer\Factory;
use UniAlteri\States\Loader\LoaderInterface;

/**
 * Class FactoryTest
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
     * @return Factory
     */
    public function createWriter()
    {
        return new Factory(
            $this->buildFileSystemMock(),
            'foo/bar'
        );
    }

    public function testCreateStandardFactoryFailure()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals(LoaderInterface::FACTORY_FILE_NAME, $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\Factory\\Standard;'));
                    $this->assertNotFalse(strpos($code, 'class '.LoaderInterface::FACTORY_CLASS_NAME.' extends Standard'));
                    return 0;
                }
            );

        $this->assertFalse($this->createWriter()->createStandardFactory('fooBar', 'Acme\\NameProduct'));
    }

    public function testCreateIntegratedFactoryFailure()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals(LoaderInterface::FACTORY_FILE_NAME, $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\Factory\\Standard;'));
                    $this->assertNotFalse(strpos($code, 'class '.LoaderInterface::FACTORY_CLASS_NAME.' extends Standard'));
                    return 10;
                }
            );

        $this->assertTrue($this->createWriter()->createStandardFactory('fooBar', 'Acme\\NameProduct'));
    }

    public function testCreateStandardFactory()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals(LoaderInterface::FACTORY_FILE_NAME, $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\Factory\\Integrated;'));
                    $this->assertNotFalse(strpos($code, 'class '.LoaderInterface::FACTORY_CLASS_NAME.' extends Integrated'));
                    return 0;
                }
            );

        $this->assertFalse($this->createWriter()->createIntegratedFactory('fooBar', 'Acme\\NameProduct'));
    }

    public function testCreateIntegratedFactory()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals(LoaderInterface::FACTORY_FILE_NAME, $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\Factory\\Integrated;'));
                    $this->assertNotFalse(strpos($code, 'class '.LoaderInterface::FACTORY_CLASS_NAME.' extends Integrated'));
                    return 10;
                }
            );

        $this->assertTrue($this->createWriter()->createIntegratedFactory('fooBar', 'Acme\\NameProduct'));
    }
}