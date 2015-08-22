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

namespace UniAlteri\States;

use UniAlteri\States\Factory\FactoryInterface;

defined('UA_STATES_PATH') || define('UA_STATES_PATH', __DIR__);

//Needed for test, but we can use your own autoloader to load file of this lib.
//This lib respects PSR-0, PSR-1 and PSR-2
$iniFile = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'autoloader_psr0.php';
if (is_readable($iniFile)) {
    include_once $iniFile;
}

//Initial DI Container
$diContainer = new DI\Container();

//Initialize the Factory Repository
$diContainer->registerInstance(FactoryInterface::DI_FACTORY_REPOSITORY, new DI\Container());

/**
 * Service to generate a finder for Stated class factory
 * @param DI\ContainerInterface $container
 * @return Loader\FinderIntegrated
 * @throws Exception\UnavailableFactory if the local factory is not available
 */
$finderService = function (DI\ContainerInterface $container) {
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
    if (!defined('DISABLE_PHP_FLOC_OPERATOR') && '5.6' <= PHP_VERSION) {
        return new DI\InjectionClosurePHP56();
    } else {
        return new DI\InjectionClosure();
    }
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
    true,
    true
);

//Return the loader for the caller file
return $loader;
