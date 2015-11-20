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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Command\Parser;

use Gaufrette\Filesystem;
use Teknoo\States\Command\Parser\Proxy;
use Teknoo\States\Command\Parser\State;
use Teknoo\States\Command\Parser\Factory;
use Teknoo\States\Command\Parser\StatedClass;
use Teknoo\States\Loader\FinderInterface;

/**
 * Class StatedClassTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StatedClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Proxy
     */
    protected $proxyParser;

    /**
     * @var Factory
     */
    protected $factoryParser;

    /**
     * @var State
     */
    protected $stateParser;

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
     * @return \PHPUnit_Framework_MockObject_MockObject|Proxy
     */
    protected function buildProxyMock()
    {
        if (!$this->proxyParser instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->proxyParser = $this->getMock(
                '\Teknoo\States\Command\Parser\Proxy',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->proxyParser;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|State
     */
    protected function buildStateMock()
    {
        if (!$this->stateParser instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->stateParser = $this->getMock(
                'Teknoo\States\Command\Parser\State',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->stateParser;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Factory
     */
    protected function buildFactoryMock()
    {
        if (!$this->factoryParser instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->factoryParser = $this->getMock(
                'Teknoo\States\Command\Parser\Factory',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->factoryParser;
    }

    /**
     * @return StatedClass
     */
    public function buildParser()
    {
        return new StatedClass(
            $this->buildFileSystemMock(),
            'Vendor/Projet/ClassNameBar',
            $this->buildFactoryMock(),
            $this->buildProxyMock(),
            $this->buildStateMock()
        );
    }

    public function testHasStatesFolderNoFolder()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    'file2.txt',
                    'file3.php',
                ]
            );

        $this->assertFalse($this->buildParser()->hasStatesFolder());
    }

    public function testHasStatesFolder()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    FinderInterface::STATES_PATH,
                    'file2.txt',
                    'file3.php',
                ]
            );

        $this->assertTrue($this->buildParser()->hasStatesFolder());
    }

    public function testHasProxyNo()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    'file2.txt',
                    'file3.php',
                ]
            );

        $this->assertFalse($this->buildParser()->hasProxy());
    }

    public function testHasProxyYes()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    'file2.txt',
                    'ClassNameBar.php',
                    'file3.php',
                ]
            );

        $this->assertTrue($this->buildParser()->hasProxy());
    }

    public function testHasFactoryNo()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    'file2.txt',
                    'ClassNameBar.php',
                    'file3.php',
                ]
            );

        $this->assertFalse($this->buildParser()->hasFactory());
    }

    public function testHasFactoryYes()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    '.',
                    '..',
                    'file1.php',
                    'file2.txt',
                    'Factory.php',
                    'file3.php',
                ]
            );

        $this->assertTrue($this->buildParser()->hasFactory());
    }

    public function testGetFactoryParser()
    {
        $this->assertSame(
            $this->buildFactoryMock(),
            $this->buildParser()->getFactoryParser()
        );
    }

    public function testGetProxyParser()
    {
        $this->assertSame(
            $this->buildProxyMock(),
            $this->buildParser()->getProxyParser()
        );
    }

    public function testGetStatesParser()
    {
        $this->assertSame(
            $this->buildStateMock(),
            $this->buildParser()->getStatesParser()
        );
    }
}
