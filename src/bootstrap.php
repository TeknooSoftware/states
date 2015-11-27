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

namespace Teknoo\States;

//Use composer has default auto loader
$composerFile = __DIR__.'/../../../../vendor/autoload.php';
if (!file_exists($composerFile)) {
    $composerFile = __DIR__.'/../vendor/autoload.php';
}
$composerInstance = require $composerFile;

/*
 * Service to generate a finder for Stated class factory
 * @param string $statedClassName
 * @param string $path
 * @return Loader\FinderComposerIntegrated
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
