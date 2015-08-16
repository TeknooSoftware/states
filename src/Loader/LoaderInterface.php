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

use UniAlteri\States\Factory\FactoryInterface;

/**
 * Interface LoaderInterface
 * It is used to allow php to load automatically stated classes without a specific behavior from the developer.
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
     * File name of the Factory PHP Class of each Stated Class.
     */
    const FACTORY_FILE_NAME = 'Factory.php';

    /**
     * Class name of the Factory PHP Class of each Stated Class.
     */
    const FACTORY_CLASS_NAME = 'Factory';

    /**
     * Return the factory used to create new finder for all new factory
     *
     * @return callable
     */
    public function getFinderFactory();

    /**
     * Return the factory repository passed to all factory loaded by this loader
     *
     * @return \ArrayAccess
     */
    public function getFactoryRepository();

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations.
     * @api
     * @param string $namespace
     * @param string $path
     *
     * @return LoaderInterface
     */
    public function registerNamespace(\string $namespace, \string $path): LoaderInterface;

    /**
     * Method called to load a class by __autoload of PHP Engine.
     * The class name can be the canonical stated class name or the canonical proxy class name of the stated class.
     *
     * @api
     * @param string $className canonical class name
     *
     * @return bool
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     * @throws \Exception
     */
    public function loadClass(\string $className): \bool;

    /**
     * Build the factory and initialize the loading stated class.
     * A new finder is built from the finder factory and must be injected in the factory with other stated class options
     *
     * @param string $factoryClassName
     * @param string $statedClassName
     * @param string $path
     *
     * @return FactoryInterface
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory(
        \string $factoryClassName,
        \string $statedClassName,
        \string $path
    ): FactoryInterface;
}
