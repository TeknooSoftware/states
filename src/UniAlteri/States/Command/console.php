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
 *
 * @version     0.9.2
 */

namespace UniAlteri\States\Command;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Symfony\Component\Console\Application;
use UniAlteri\States\Command\Parser\AbstractParser;
use UniAlteri\States\Command\Writer\AbstractWriter;

require_once dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR.'autoloader_psr0.php';

/*
 * @param string $directory
 * @return Filesystem
 */
$fileSystemFactory = function ($directory) {
    return new Filesystem(
        new Local(
            realpath($directory)
        )
    );
};

/*
 * @param string $service
 * @param string $destinationPath
 * @return AbstractWriter|AbstractParser
 * @throws \Exception when $service is bad
 */
$factory = function ($service, $destinationPath) use ($fileSystemFactory) {
    $fileSystem = $fileSystemFactory($destinationPath);
    switch ($service) {
        case 'Parser\Factory':
            return new Parser\Factory($fileSystem, $destinationPath);
            break;
        case 'Parser\Proxy':
            return new Parser\Proxy($fileSystem, $destinationPath);
            break;
        case 'Parser\State':
            return new Parser\State($fileSystem, $destinationPath);
            break;
        case 'Parser\StatedClass':
            return new Parser\StatedClass(
                $fileSystem,
                $destinationPath,
                new Parser\Factory($fileSystem, $destinationPath),
                new Parser\Proxy($fileSystem, $destinationPath),
                new Parser\State($fileSystem, $destinationPath)
            );
            break;
        case 'Writer\Factory':
            return new Writer\Factory($fileSystem, $destinationPath);
            break;
        case 'Writer\Proxy':
            return new Writer\Proxy($fileSystem, $destinationPath);
            break;
        case 'Writer\State':
            return new Writer\State($fileSystem, $destinationPath);
            break;
        default:
            throw new \Exception('Bad required service');
            break;
    }
};

$application = new Application();
$application->add(new ClassCreate(null, $factory, $fileSystemFactory));
$application->add(new ClassInformation(null, $factory, $fileSystemFactory));
$application->add(new StateAdd(null, $factory, $fileSystemFactory));
$application->add(new StateList(null, $factory, $fileSystemFactory));

return $application;
