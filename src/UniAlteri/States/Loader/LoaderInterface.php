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
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.2
 */
namespace UniAlteri\States\Loader;

use UniAlteri\States\DI;
use UniAlteri\States\Factory;

/**
 * Interface LoaderInterface
 * Interface to define a "stated class autoloader" to allow php to load automatically stated class.
 *
 * @package     States
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @api
 */
interface LoaderInterface
{
    /**
     * Name of Finder (service to find and load elements of stated class)
     */
    const DI_LOADER_INSTANCE = 'ClassLoader';

    /**
     * PHP File of Factory into each stated class
     */
    const FACTORY_FILE_NAME = 'Factory.php';

    /**
     * Class name of the Factory PHP Class of each Stated Class
     */
    const FACTORY_CLASS_NAME = 'Factory';

    /**
     * Initialize the loader object
     * @param  IncludePathManagerInterface $includePathManager
     * @throws Exception\IllegalArgument   $includePathManager does not implement the interface IncludePathManagerInterface
     */
    public function __construct($includePathManager);

    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container);

    /**
     * To return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * Method to add a path on the list of location where find class
     * @param  string                    $path
     * @return $this
     * @throws Exception\UnavailablePath if the path is not readable
     */
    public function addIncludePath($path);

    /**
     * To list all active included path for this loaded
     * @return string[]
     */
    public function getIncludedPaths();

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations
     * @param  string                    $namespace
     * @param  string                    $path
     * @return $this
     * @throws Exception\IllegalArgument if the path is not a valid string
     */
    public function registerNamespace($namespace, $path);

    /**
     * To list all registered namespace
     * @return \ArrayObject
     */
    public function listNamespaces();

    /**
     * Method called to load a class by __autoload of PHP Engine
     * @param  string                       $className class name, support namespace prefixes
     * @return boolean
     * @throws \Exception
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function loadClass($className);

    /**
     * Build the factory and initialize the loading stated class
     * @param  string                       $factoryClassName
     * @param  string                       $statedClassName
     * @param  string                       $path
     * @return Factory\FactoryInterface
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory($factoryClassName, $statedClassName, $path);
}
