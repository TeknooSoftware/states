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
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Command;

use Teknoo\States\Command\ClassCreate;
use Teknoo\States\Command\ClassInformation;
use Teknoo\States\Command\Parser\StatedClass;

/**
 * Class ClassInformationTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers Teknoo\States\Command\ClassInformation
 * @covers Teknoo\States\Command\AbstractCommand
 */
class ClassInformationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatedClass
     */
    protected $parser;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StatedClass
     */
    protected function buildStatedClassMock()
    {
        if (!$this->parser instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->parser = $this->getMock(
                'Teknoo\States\Command\Parser\StatedClass',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->parser;
    }

    /**
     * @return ClassCreate
     */
    protected function buildCommand()
    {
        return new ClassInformation(
            null,
            function ($service, $destinationPath) {
                switch ($service) {
                    case 'Parser\StatedClass':
                        return $this->buildStatedClassMock();
                        break;
                    default:
                        $this->fail('Error, bad service called');
                        break;
                }
            }
        );
    }

    public function testExecuteFalse()
    {
        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface', [], [], '', false);
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['path', 'path/to/stated/class'],
                ]
            );

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface', [], [], '', false);

        $output->expects($this->any())
            ->method('write')
            ->withConsecutive(
                ['Proxy defined: false', true, 0],
                ['Proxy is valid: false', true, 0],
                ['Proxy is standard: false', true, 0],
                ['Proxy is integrated: false', true, 0],
                ['Factory defined: false', true, 0],
                ['Factory is valid: false', true, 0],
                ['Factory is standard: false', true, 0],
                ['Factory is integrated: false', true, 0],
                ['States: State1, State2, State3', true, 0]
            );

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('hasProxy')
            ->willReturn(false);

        $parserParser = $this->getMock('Teknoo\States\Command\Parser\Proxy', [], [], '', false);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getProxyParser')
            ->willReturn($parserParser);

        $parserParser->expects($this->once())
            ->method('isValidProxy')
            ->willReturn(false);

        $parserParser->expects($this->once())
            ->method('isStandardProxy')
            ->willReturn(false);

        $parserParser->expects($this->once())
            ->method('isIntegratedProxy')
            ->willReturn(false);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('hasFactory')
            ->willReturn(false);

        $factoryParser = $this->getMock('Teknoo\States\Command\Parser\Factory', [], [], '', false);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getFactoryParser')
            ->willReturn($factoryParser);

        $factoryParser->expects($this->once())
            ->method('isValidFactory')
            ->willReturn(false);

        $factoryParser->expects($this->once())
            ->method('isStandardFactory')
            ->willReturn(false);

        $factoryParser->expects($this->once())
            ->method('isIntegratedFactory')
            ->willReturn(false);

        $stateParser = $this->getMock('Teknoo\States\Command\Parser\State', [], [], '', false);
        $stateParser->expects($this->any())
            ->method('listStates')
            ->willReturn(new \ArrayObject(['State1', 'State2', 'State3']));

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getStatesParser')
            ->willReturn($stateParser);

        $command = $this->buildCommand();
        $command->run($input, $output);
    }

    public function testExecuteTrue()
    {
        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface', [], [], '', false);
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['path', 'path/to/stated/class'],
                ]
            );

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface', [], [], '', false);

        $output->expects($this->any())
            ->method('write')
            ->withConsecutive(
                ['Proxy defined: true', true, 0],
                ['Proxy is valid: true', true, 0],
                ['Proxy is standard: true', true, 0],
                ['Proxy is integrated: true', true, 0],
                ['Factory defined: true', true, 0],
                ['Factory is valid: true', true, 0],
                ['Factory is standard: true', true, 0],
                ['Factory is integrated: true', true, 0],
                ['States: State1, State2, State3', true, 0]
            );

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('hasProxy')
            ->willReturn(true);

        $parserParser = $this->getMock('Teknoo\States\Command\Parser\Proxy', [], [], '', false);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getProxyParser')
            ->willReturn($parserParser);

        $parserParser->expects($this->once())
            ->method('isValidProxy')
            ->willReturn(true);

        $parserParser->expects($this->once())
            ->method('isStandardProxy')
            ->willReturn(true);

        $parserParser->expects($this->once())
            ->method('isIntegratedProxy')
            ->willReturn(true);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('hasFactory')
            ->willReturn(true);

        $factoryParser = $this->getMock('Teknoo\States\Command\Parser\Factory', [], [], '', false);

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getFactoryParser')
            ->willReturn($factoryParser);

        $factoryParser->expects($this->once())
            ->method('isValidFactory')
            ->willReturn(true);

        $factoryParser->expects($this->once())
            ->method('isStandardFactory')
            ->willReturn(true);

        $factoryParser->expects($this->once())
            ->method('isIntegratedFactory')
            ->willReturn(true);

        $stateParser = $this->getMock('Teknoo\States\Command\Parser\State', [], [], '', false);
        $stateParser->expects($this->any())
            ->method('listStates')
            ->willReturn(new \ArrayObject(['State1', 'State2', 'State3']));

        $this->buildStatedClassMock()
            ->expects($this->once())
            ->method('getStatesParser')
            ->willReturn($stateParser);

        $command = $this->buildCommand();
        $command->run($input, $output);
    }
}
