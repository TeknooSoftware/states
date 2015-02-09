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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.2
 */
namespace UniAlteri\Tests\States\Command;

use UniAlteri\States\Command\Parser\State;
use UniAlteri\States\Command\StateList;

/**
 * Class StateListTest
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    protected $parser;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|State
     */
    protected function buildStateClassMock()
    {
        if (!$this->parser instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->parser = $this->getMock(
                'UniAlteri\States\Command\Parser\State',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->parser;
    }

    /**
     * @return StateList
     */
    protected function buildCommand()
    {
        return new StateList(
            null,
            function ($service, $destinationPath) {
                switch ($service) {
                    case 'Parser\State':
                        return $this->buildStateClassMock();
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
            ->with($this->equalTo(implode(PHP_EOL, ['State1', 'State2', 'State3'])));

        $this->buildStateClassMock()
            ->expects($this->any())
            ->method('listStates')
            ->willReturn(new \ArrayObject(['State1', 'State2', 'State3']));

        $command = $this->buildCommand();
        $command->run($input, $output);
    }
}
