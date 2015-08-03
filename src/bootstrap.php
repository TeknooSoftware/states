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
$composerFile = __DIR__.'/../../vendor/autoload.php';
if (!file_exists($composerFile)) {
    $composerFile = __DIR__.'/../vendor/autoload.php';
}
$composerInstance = require $composerFile;

/**
 * Service to generate a finder for Stated class factory
 * @param string $statedClassName
 * @param string $path
 * @return Loader\FinderComposerIntegrated
 * @throws Exception\UnavailableFactory if the local factory is not available
 */
$finderFactory = function (string $statedClassName, string $path) use ($composerInstance) {
    return new Loader\FinderComposerIntegrated($statedClassName, $path, $composerInstance);
};

$factoryRepository = new \ArrayObject();
$loader = new Loader\LoaderComposer($composerInstance, $finderFactory, $factoryRepository);

//Register autoload function in the spl autoloader stack
spl_autoload_register(
    array($loader, 'loadClass'),
    true,
    true
);

//Return the loader for the caller file
return $loader;
