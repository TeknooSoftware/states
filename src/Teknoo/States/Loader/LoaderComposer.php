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
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Loader;

use Composer\Autoload\ClassLoader;
use Teknoo\States\DI;
use Teknoo\States\Factory;

/**
 * Class LoaderStandard
 * Default implementation of the "stated class autoloader".
 * It is used to allow php to load automatically stated class. It builds on the Composer Loader. It is registered to be
 * called before the composer loader, find the factory attached to the stated class, load and run it to initialize the
 * stated class.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @api
 */
class LoaderComposer implements LoaderInterface
{
    /**
     * DI Container to use with this loader.
     *
     * @var DI\ContainerInterface
     */
    protected $diContainer;

    /**
     * @var ClassLoader
     */
    protected $composerInstance;

    /**
     * @var string[]
     */
    protected $loadingFactoriesClassNameArray = [];

    /**
     * To keep the list of factory already fetched.
     *
     * @var array
     */
    private $factoryAvailabilityList = array();

    /**
     * Initialize the loader object.
     *
     * @param ClassLoader $composerInstance
     */
    public function __construct($composerInstance)
    {
        $this->composerInstance = $composerInstance;

        if (class_exists('\Phar', false)) {
            //instructs phar to intercept fopen, file_get_contents, opendir, and all of the stat-related functions
            //Needed to support Phar with the loader
            \Phar::interceptFileFuncs();
        }
    }

    /**
     * To register a DI container for this object.
     *
     * @api
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object.
     *
     * @api
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->diContainer;
    }

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations.
     *
     * @api
     *
     * @param string $namespace
     * @param string $path
     *
     * @return $this
     */
    public function registerNamespace($namespace, $path)
    {
        if ('\\' !== $namespace[strlen($namespace) - 1]) {
            $namespace = ltrim($namespace, '\\').'\\';
        }

        $this->composerInstance->addPsr4($namespace, $path);

        return $this;
    }

    /**
     * To load the factory of the stated class and check if it's implementing the good interface.
     *
     * @param string $factoryClassName
     *
     * @return bool
     */
    private function loadFactory(&$factoryClassName)
    {
        if (!isset($this->factoryAvailabilityList[$factoryClassName])) {
            if (true === class_exists($factoryClassName, false)
                || true === $this->composerInstance->loadClass($factoryClassName)) {
                $reflectionClassInstance = new \ReflectionClass($factoryClassName);

                $this->factoryAvailabilityList[$factoryClassName] = $reflectionClassInstance->implementsInterface('Teknoo\\States\\Factory\\FactoryInterface');
            } else {
                $this->factoryAvailabilityList[$factoryClassName] = false;
            }
        }

        return $this->factoryAvailabilityList[$factoryClassName];
    }

    /**
     * Method called to load a class by __autoload of PHP Engine.
     *
     * @api
     *
     * @param string $className class name, support namespace prefixes
     *
     * @return bool
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     * @throws \Exception
     */
    public function loadClass($className)
    {
        if (isset($this->loadingFactoriesClassNameArray[$className])
            || 0 === strpos($className, 'Teknoo\\States')
            || 0 === strpos($className, 'Teknoo\\Tests\\States')) {
            return false;
        }

        $factoryClassName = $className.'\\'.LoaderInterface::FACTORY_CLASS_NAME;
        $this->loadingFactoriesClassNameArray[$factoryClassName] = true;

        $statedClassName = $className;
        $factoryClassFound = $this->loadFactory($factoryClassName);

        if (false === $factoryClassFound) {
            $canonicalClassNameParts = explode('\\', $className);
            array_pop($canonicalClassNameParts);
            $statedClassName = implode('\\', $canonicalClassNameParts);

            $factoryClassName = $statedClassName.'\\'.LoaderInterface::FACTORY_CLASS_NAME;
            $this->loadingFactoriesClassNameArray[$factoryClassName] = true;

            $factoryClassFound = $this->loadFactory($factoryClassName);
        }

        if (true === $factoryClassFound) {
            $this->buildFactory(
                $factoryClassName,
                $statedClassName,
                dirname($this->composerInstance->findFile($factoryClassName))
            );

            return true;
        }

        return false;
    }

    /**
     * Build the factory and initialize the loading stated class.
     *
     * @internal
     *
     * @param string $factoryClassName
     * @param string $statedClassName
     * @param string $path
     *
     * @return Factory\FactoryInterface
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory($factoryClassName, $statedClassName, $path)
    {
        //Check if the factory class is loaded
        if (!class_exists($factoryClassName, false)) {
            throw new Exception\UnavailableFactory(
                sprintf('The factory of %s is not available', $statedClassName)
            );
        }

        //Create a new instance of the factory
        $factoryObject = new $factoryClassName();
        if (!$factoryObject instanceof Factory\FactoryInterface) {
            throw new Exception\IllegalFactory(
                sprintf('The factory of %s does not implement the interface', $statedClassName)
            );
        }

        //clone the di container for this stated class, it will has its own di container
        if ($this->diContainer instanceof DI\ContainerInterface) {
            $diContainer = clone $this->diContainer;
            $factoryObject->setDIContainer($diContainer);
        }

        //Call its initialize methods to load the stated class
        $factoryObject->initialize($statedClassName, $path);

        return $factoryObject;
    }

    /**
     * Method to add a path on the list of location where find class.
     *
     * @param string $path
     *
     * @return $this
     *
     * @throws Exception\UnavailablePath if the path is not readable
     *
     * @deprecated  Removed since 2.0
     */
    public function addIncludePath($path)
    {
        return $this;
    }

    /**
     * To list all active included path for this loaded.
     *
     * @return string[]
     *
     * @deprecated  Removed since 2.0
     */
    public function getIncludedPaths()
    {
        return new \ArrayObject([]);
    }

    /**
     * To list all registered namespace.
     *
     * @return \ArrayObject
     *
     * @deprecated  Removed since 2.0
     */
    public function listNamespaces()
    {
        return new \ArrayObject([]);
    }
}
