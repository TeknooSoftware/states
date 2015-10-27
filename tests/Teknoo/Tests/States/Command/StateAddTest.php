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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\Tests\States\Command;

use Teknoo\States\Command\StateAdd;
use Teknoo\States\Command\Writer\State;

/**
 * Class StateAddTest.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateAddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|State
     */
    protected function buildStateMock()
    {
        if (!$this->state instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->state = $this->getMock(
                'Teknoo\States\Command\Writer\State',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->state;
    }

    /**
     * @return StateAdd
     */
    protected function buildCommand()
    {
        return new StateAdd(
            null,
            function ($service, $destinationPath) {
                switch ($service) {
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

    public function testCreateState()
    {
        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface', [], [], '', false);
        $input->expects($this->any())
            ->method('getArgument')
            ->willReturnMap(
                [
                    ['className', '\\vendor\\project\\package\\fooBar'],
                    ['name', 'newStateClass'],
                ]
            );

        $input->expects($this->any())
            ->method('getOption')
            ->willReturnMap(
                [
                    ['path', 'path/to/class'],
                ]
            );

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface', [], [], '', false);

        $this->buildStateMock()
            ->expects($this->once())
            ->method('createState')
            ->with(
                $this->equalTo('fooBar'),
                $this->equalTo('\\vendor\\project\\package'),
                $this->equalTo('newStateClass')
            );

        $command = $this->buildCommand();
        $command->run($input, $output);
    }
}
