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
namespace Teknoo\Tests\States\Command;

use Teknoo\States\Command\ClassCreate;
use Teknoo\States\Command\Writer\Factory;
use Teknoo\States\Command\Writer\Proxy;
use Teknoo\States\Command\Writer\State;

/**
 * Class ClassCreateTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers Teknoo\States\Command\ClassCreate
 * @covers Teknoo\States\Command\AbstractCommand
 */
class ClassCreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var string
     */
    protected $pathCalled;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Factory
     */
    protected function buildFactoryMock()
    {
        if (!$this->factory instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->factory = $this->createMock(
                'Teknoo\States\Command\Writer\Factory');
        }

        return $this->factory;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Proxy
     */
    protected function buildProxyMock()
    {
        if (!$this->proxy instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->proxy = $this->createMock(
                'Teknoo\States\Command\Writer\Proxy');
        }

        return $this->proxy;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|State
     */
    protected function buildStateMock()
    {
        if (!$this->state instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->state = $this->createMock(
                'Teknoo\States\Command\Writer\State');
        }

        return $this->state;
    }

    /**
     * @return ClassCreate
     */
    protected function buildCommand()
    {
        return new ClassCreate(
            null,
            function ($service, $destinationPath) {
                $this->pathCalled = $destinationPath;
                switch ($service) {
                    case 'Writer\Factory':
                        return $this->buildFactoryMock();
                        break;
                    case 'Writer\Proxy':
                        return $this->buildProxyMock();
                        break;
                    case 'Writer\State':
                        return $this->buildStateMock();
                        break;
                    default:
                        $this->fail('Error, bad service called');
                        break;
                }
            }
        );
    }

    public function testCreateStandardClass()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['className', '\\vendor\\project\\package\\fooBar'],
                ]
            );

        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap(
                [
                    ['mode', 'standard'],
                    ['path', 'path/to/class/vendor/project'],
                ]
            );

        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $this->buildProxyMock()
            ->expects($this->once())
            ->method('createStandardProxy')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $this->buildFactoryMock()
            ->expects($this->once())
            ->method('createStandardFactory')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $command = $this->buildCommand();
        $command->run($input, $output);

        $this->assertEquals('path/to/class/vendor/project/package/fooBar', $this->pathCalled);
    }

    public function testCreateIntegratedClass()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['className', '\\vendor\\project\\package\\fooBar'],
                ]
            );

        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap(
                [
                    ['mode', 'integrated'],
                    ['path', 'path/to/class/vendor/project/package'],
                ]
            );

        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $this->buildProxyMock()
            ->expects($this->once())
            ->method('createIntegratedProxy')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $this->buildFactoryMock()
            ->expects($this->once())
            ->method('createIntegratedFactory')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $command = $this->buildCommand();
        $command->run($input, $output);

        $this->assertEquals('path/to/class/vendor/project/package/fooBar', $this->pathCalled);
    }

    public function testCreateIntegratedClassMissingDestinationFolder()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['className', '\\vendor\\project\\package\\fooBar'],
                ]
            );

        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap(
                [
                    ['mode', 'integrated'],
                    ['path', 'path/to/class'],
                ]
            );

        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $this->buildProxyMock()
            ->expects($this->once())
            ->method('createIntegratedProxy')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $this->buildFactoryMock()
            ->expects($this->once())
            ->method('createIntegratedFactory')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $command = $this->buildCommand();
        $command->run($input, $output);

        $this->assertEquals('path/to/class/vendor/project/package/fooBar', $this->pathCalled);
    }

    public function testCreateIntegratedClassExistingDestinationFolder()
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['className', '\\vendor\\project\\package\\fooBar'],
                ]
            );

        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap(
                [
                    ['mode', 'integrated'],
                    ['path', 'path/to/class/vendor/project/package/fooBar'],
                ]
            );

        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $this->buildProxyMock()
            ->expects($this->once())
            ->method('createIntegratedProxy')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $this->buildFactoryMock()
            ->expects($this->once())
            ->method('createIntegratedFactory')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package')
            );

        $command = $this->buildCommand();
        $command->run($input, $output);

        $this->assertEquals('path/to/class/vendor/project/package/fooBar', $this->pathCalled);
    }
}
