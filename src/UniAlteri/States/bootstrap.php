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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States;

use UniAlteri\States\Factory\FactoryInterface;

defined('UA_STATES_PATH')
    || define('UA_STATES_PATH', __DIR__);

//Shortcut for DIRECTORY_SEPARATOR
defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Use composer has default auto loader
$composerInstance = require_once __DIR__.'/../../../vendor/autoload.php';

//Initial DI Container
$diContainer = new DI\Container();

//Initialize the Factory Repository
$diContainer->registerInstance(FactoryInterface::DI_FACTORY_REPOSITORY, new DI\Container());

/*
 * Service to generate a finder for Stated class factory
 * @param DI\ContainerInterface $container
 * @return Loader\FinderIntegrated
 * @throws Exception\UnavailableFactory if the local factory is not available
 */
$finderService = function (DI\ContainerInterface $container) use ($composerInstance) {
    if (false === $container->testEntry(Factory\FactoryInterface::DI_FACTORY_NAME)) {
        throw new Exception\UnavailableFactory('Error, the factory is not available into container');
    }

    $factory = $container->get(Factory\FactoryInterface::DI_FACTORY_NAME);

    return new Loader\FinderIntegrated($factory->getStatedClassName(), $factory->getPath(), $composerInstance);
};

//Register finder generator
$diContainer->registerService(Loader\FinderInterface::DI_FINDER_SERVICE, $finderService);

$loader = new Loader\LoaderComposer($composerInstance);
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
