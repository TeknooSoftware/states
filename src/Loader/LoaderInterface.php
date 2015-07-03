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

namespace UniAlteri\States\Loader;

use UniAlteri\States\DI;
use UniAlteri\States\Factory;

/**
 * Interface LoaderInterface
 * Interface to define a "stated class autoloader" to allow php to load automatically stated class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @api
 */
interface LoaderInterface
{
    /**
     * Name of Finder (service to find and load elements of stated class).
     */
    const DI_LOADER_INSTANCE = 'ClassLoader';

    /**
     * PHP File of Factory into each stated class.
     */
    const FACTORY_FILE_NAME = 'Factory.php';

    /**
     * Class name of the Factory PHP Class of each Stated Class.
     */
    const FACTORY_CLASS_NAME = 'Factory';

    /**
     * To register a DI container for this object.
     * @api
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container);

    /**
     * To return the DI Container used for this object.
     * @api
     * @return DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations.
     * @api
     * @param string $namespace
     * @param string $path
     *
     * @return $this
     */
    public function registerNamespace(string $namespace, string $path): LoaderInterface;

    /**
     * Method called to load a class by __autoload of PHP Engine.
     * @api
     * @param string $className class name, support namespace prefixes
     *
     * @return bool
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     * @throws \Exception
     */
    public function loadClass(string $className): bool;

    /**
     * Build the factory and initialize the loading stated class.
     * @internal
     * @param string $factoryClassName
     * @param string $statedClassName
     * @param string $path
     *
     * @return Factory\FactoryInterface
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory(string $factoryClassName, string $statedClassName, string $path): Factory\FactoryInterface;
}
