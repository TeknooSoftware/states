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
 * @subpackage  Bootstraping
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States;

use \UniAlteri\States\Exception;
use \UniAlteri\States\DI;
use \UniAlteri\States\Loader;
use \UniAlteri\States\Factory;

//Needed for test, but we can use your own autloader to load file of this lib.
//This lib respects PSR-0 and PSR-1
$iniFile = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'bootstrap.php';
if (is_readable($iniFile)) {
    include_once($iniFile);
}

//Initial DI Container
$diContainer = new DI\Container();

//Service to generate a finder for Stated class factory
/**
 * @param DI\ContainerInterface $container
 * @return Loader\FinderIntegrated
 * @throws Exception\UnavailableFactory if the local factory is not availables
 */
$finderService = function ($container) {
    if (false === $container->testEntry(Factory\FactoryInterface::DI_FACTORY_NAME)) {
        throw new Exception\UnavailableFactory('Error, the factory is not available into container');
    }

    $factory = $container->get(Factory\FactoryInterface::DI_FACTORY_NAME);
    return new Loader\FinderIntegrated($factory->getStatedClassName(), $factory->getPath());
};

//Register finder generator
$diContainer->registerService(Loader\FinderInterface::DI_FINDER_SERVICE, $finderService);

//Register injection closure generator
$injectionClosureService = function () {
    return new DI\InjectionClosure();
};

$diContainer->registerService(States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER, $injectionClosureService);

//Stated class loader, initialize
$includePathManager = new Loader\IncludePathManager();
$loader = new Loader\LoaderStandard($includePathManager);
$loader->setDIContainer($diContainer);

//Register loader into container
$diContainer->registerInstance(Loader\LoaderInterface::DI_LOADER_INSTANCE, $loader);

//Register autoload function in the spl autoloader stack
spl_autoload_register(
    array($loader, 'loadClass'),
    true
);

//Return the loader for the caller file
return $loader;