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

/**
 * Class ConsoleTest
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testConsole()
    {
        $application = include 'UniAlteri/States/Command/console.php';
        $this->assertInstanceOf('Symfony\Component\Console\Application', $application);

        $this->assertEquals(
            [
                'help',
                'list',
                'class:create',
                'class:info',
                'state:add',
                'state:list',
            ],
            array_keys($application->all())
        );
    }

    public function testFileSystemFactory()
    {
        $application = include 'UniAlteri/States/Command/console.php';
        $command = $application->get('class:create');
        $fileSystemFactory = $command->getFileSystemFactory();

        $this->assertEquals('Closure', get_class($fileSystemFactory));
        $this->assertInstanceOf('Gaufrette\Filesystem', $fileSystemFactory('path'));
    }

    public function testFactory()
    {
        $application = include 'UniAlteri/States/Command/console.php';
        $command = $application->get('class:create');
        $factory = $command->getFactory();

        $this->assertEquals('Closure', get_class($factory));
        $this->assertInstanceOf('UniAlteri\States\Command\Parser\Factory', $factory('Parser\Factory', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Parser\Proxy', $factory('Parser\Proxy', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Parser\State', $factory('Parser\State', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Parser\StatedClass', $factory('Parser\StatedClass', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Writer\Factory', $factory('Writer\Factory', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Writer\Proxy', $factory('Writer\Proxy', 'path'));
        $this->assertInstanceOf('UniAlteri\States\Command\Writer\State', $factory('Writer\State', 'path'));
        try {
            $factory('BadService', 'path');
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error the parser and writer factory must throw an exception when the service is not valid');
    }
}
