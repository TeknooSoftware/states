<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\States;

use UniAlteri\States;
use UniAlteri\States\Loader;
use UniAlteri\States\Factory;
use UniAlteri\States\Exception;
use UniAlteri\Tests\Support;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    public function testLoaderInitialisation()
    {
        $loader = include('UniAlteri/States/bootstrap.php');
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\LoaderStandard', $loader);

        $container = $loader->getDIContainer();
        $this->assertInstanceOf('\\UniAlteri\\States\\DI\\Container', $container);

        $this->assertTrue($container->testEntry(Loader\FinderInterface::DI_FINDER_SERVICE));
        $this->assertTrue($container->testEntry(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER));

        $fail = false;
        try {
            $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        } catch (Exception\UnavailableFactory $e) {
            $fail = true;
        } catch (\Exception $e) {}

        $this->assertTrue($fail);

        $container->registerInstance(Factory\FactoryInterface::DI_FACTORY_NAME, new Support\VirtualFactory());
        $finder = $container->get(Loader\FinderInterface::DI_FINDER_SERVICE);
        $this->assertInstanceOf('\\UniAlteri\\States\\Loader\\FinderIntegrated', $finder);

        $injectionClosure = $container->get(States\States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        $this->assertInstanceOf('\\UniAlteri\\States\\DI\\InjectionClosure', $injectionClosure);
    }
}