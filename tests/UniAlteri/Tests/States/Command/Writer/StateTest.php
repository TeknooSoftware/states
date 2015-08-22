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
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\Command\Writer;

use Gaufrette\Filesystem;
use UniAlteri\States\Command\Writer\State;
use UniAlteri\States\Proxy\ProxyInterface;

/**
 * Class StateTest.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateTest extends \PHPUnit_Framework_TestCase
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
     * @return State
     */
    public function createWriter()
    {
        return new State(
            $this->buildFileSystemMock(),
            'foo/bar'
        );
    }

    public function testCreateDefaultStateFailure()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals('States/StateDefault.php', $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar\\States;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\States;'));
                    $this->assertNotFalse(strpos($code, 'class '.ProxyInterface::DEFAULT_STATE_NAME.' extends States\\AbstractState'));

                    return 0;
                }
            );

        $this->assertFalse($this->createWriter()->createDefaultState('fooBar', 'Acme\\NameProduct'));
    }

    public function testCreateStateFailure()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals('States/StateDefault.php', $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar\\States;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\States;'));
                    $this->assertNotFalse(strpos($code, 'class '.ProxyInterface::DEFAULT_STATE_NAME.' extends States\\AbstractState'));

                    return 10;
                }
            );

        $this->assertTrue($this->createWriter()->createDefaultState('fooBar', 'Acme\\NameProduct'));
    }

    public function testCreateDefaultState()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals('States/helloWorld.php', $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar\\States;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\States;'));
                    $this->assertNotFalse(strpos($code, 'class helloWorld extends States\\AbstractState'));

                    return 0;
                }
            );

        $this->assertFalse($this->createWriter()->createState('fooBar', 'Acme\\NameProduct', 'helloWorld'));
    }

    public function testCreateState()
    {
        $this->buildFileSystemMock()
            ->expects($this->once())
            ->method('write')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($file, $code) {
                    $this->assertEquals('States/helloWorld.php', $file);
                    $this->assertNotFalse(strpos($code, 'namespace Acme\\NameProduct\\fooBar\\States;'));
                    $this->assertNotFalse(strpos($code, 'use UniAlteri\\States\\States;'));
                    $this->assertNotFalse(strpos($code, 'class helloWorld extends States\\AbstractState'));

                    return 10;
                }
            );

        $this->assertTrue($this->createWriter()->createState('fooBar', 'Acme\\NameProduct', 'helloWorld'));
    }
}
