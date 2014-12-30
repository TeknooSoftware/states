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
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\States\Command;

use Gaufrette\Adapter\Local;
use Gaufrette\Adapter\SafeLocal;
use Symfony\Component\Console\Application;

require_once dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR.'autoloader_psr0.php';

/**
 * @param string $directory
 * @return Local
 */
$localAdapterFactory = function ($directory) {
    return new Local(realpath($directory));
};

/**
 * @param string $service
 * @param callable $adapter
 * @param string $destinationPath
 * @return AbstractWriter|AbstractParser
 */
$readerAndWriterFactory = function ($service, $adapter, $destinationPath) {
    switch ($service) {
        case 'WriterProxy':
            return Writer\Proxy($adapter, $destinationPath);
            break;
        case 'WriterFactory':
            return Writer\Factory($adapter, $destinationPath);
            break;
        case 'WriterState':
            return Writer\State($adapter, $destinationPath);
            break;
        case 'StatedClass':
            return StatedClass($adapter, $destinationPath);
            break;
    }
}

$application = new Application();
$application->add(new ClassCreate(null, $localAdapterFactory, $readerAndWriterFactory));
$application->add(new ClassInformation(null, $localAdapterFactory, $readerAndWriterFactory));
$application->add(new StateAdd(null, $localAdapterFactory, $readerAndWriterFactory));
$application->add(new StateList(null, $localAdapterFactory, $readerAndWriterFactory));
$application->run();