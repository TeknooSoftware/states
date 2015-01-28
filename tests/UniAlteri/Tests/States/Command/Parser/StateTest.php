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
 * @version     1.0.0
 */

namespace UniAlteri\Tests\States\Command\Parser;
use Gaufrette\Filesystem;
use UniAlteri\States\Command\Parser\Exception\ClassNotFound;
use UniAlteri\States\Command\Parser\State;
use UniAlteri\States\Exception\UnReadablePath;
use UniAlteri\States\Loader\FinderInterface;

/**
 * Class StateTest
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
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
     * Prepare and build a proxy parser to test
     * @param string $path
     * @return State
     */
    protected function buildStateParser($path)
    {
        return new State(
            $this->buildFileSystemMock(),
            $path
        );
    }

    public function testIsValidStateBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/BadState/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->isValidState('BadState');
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testIsValidStateBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->isValidState('BadState');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsValidStateNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/WithoutImpl';

        try {
            $this->buildStateParser($path)
                ->isValidState('WithoutImpl');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsValidState()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateNormal.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildStateParser($path)->isValidState('StateNormal'));
    }

    public function testIsStandardStateBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->isStandardState('BadState');
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testIsStandardStateBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->isStandardState('BadState');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsStandardStateNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/WithoutImpl';

        try {
            $this->buildStateParser($path)
                ->isStandardState('WithoutImpl');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testIsStandardStateNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateNormal.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildStateParser($path)->isStandardState('StateNormal'));
    }

    public function testIsStandardStateStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateStd.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildStateParser($path)->isStandardState('StateStd'));
    }

    public function testUseTraitStateBadFile()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(false);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->useTraitState('BadState');
        } catch (UnReadablePath $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class is not readable');
    }

    public function testUseTraitStateBadFileContent()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/BadState.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/BadState';

        try {
            $this->buildStateParser($path)
                ->useTraitState('BadState');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testUseTraitStateNotImplement()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/WithoutImpl.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/WithoutImpl';

        try {
            $this->buildStateParser($path)
                ->useTraitState('WithoutImpl');
        } catch (ClassNotFound $e) {
            return;
        }

        $this->fail('Error, the parser must throw an exception when the proxy class was not found');
    }

    public function testUseTraitStateNot()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateNormal.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildStateParser($path)->useTraitState('StateNormal'));
    }

    public function testUseTraitStateStd()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateStd.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertFalse($this->buildStateParser($path)->useTraitState('StateStd'));
    }

    public function testUseTraitStateTrait()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('has')
            ->with($this->equalTo('States/StateWithTrait.php'))
            ->willReturn(true);

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('read')
            ->willReturnCallback(
                function($fileName) use ($path) {
                    return file_get_contents($path.DIRECTORY_SEPARATOR.$fileName);
                }
            );

        $this->assertTrue($this->buildStateParser($path)->useTraitState('StateWithTrait'));
    }

    public function testListStates()
    {
        $this->buildFileSystemMock()
            ->expects($this->atLeastOnce())
            ->method('keys')
            ->willReturn(
                [
                    'className.php',
                    'Factory.php',
                    FinderInterface::STATES_PATH,
                    FinderInterface::STATES_PATH,
                    FinderInterface::STATES_PATH,
                    FinderInterface::STATES_PATH.DIRECTORY_SEPARATOR.'State1.php',
                    FinderInterface::STATES_PATH.DIRECTORY_SEPARATOR.'State2.php',
                    FinderInterface::STATES_PATH.DIRECTORY_SEPARATOR.'State3.php'
                ]
            );

        $path = dirname(dirname(dirname(dirname(__FILE__)))).'/Support/Command/Parser/State/Acme/GoodState';

        $states = $this->buildStateParser($path)->listStates();
        $this->assertInstanceOf('\ArrayObject', $states);
        $this->assertEquals(['State1', 'State2', 'State3'], $states->getArrayCopy());
    }
}