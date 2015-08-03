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

use Composer\Autoload\ClassLoader;
use UniAlteri\States\DI;
use UniAlteri\States\Factory;

/**
 * Class LoaderStandard
 * Default implementation of the "stated class autoloader".
 * It is used to allow php to load automatically stated class. It builds on the Composer Loader. It is registered to be
 * called before the composer loader, find the factory attached to the stated class, load and run it to initialize the
 * stated class
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
class LoaderComposer implements LoaderInterface
{
    /**
     * @var ClassLoader
     */
    protected $composerInstance;

    /**
     * @var callable
     */
    protected $finderFactory;

    /**
     * @var \ArrayAccess
     */
    protected $factoryRepository;

    /**
     * @var string[]
     */
    protected $loadingFactoriesClassNameArray = [];

    /**
     * To keep the list of factory already fetched
     * @var array
     */
    private $factoryAvailabilityList = array();

    /**
     * Initialize the loader object.
     *
     * @param ClassLoader $composerInstance
     * @param callable $finderFactory
     * @param \ArrayAccess $factoryRepository
     */
    public function __construct(ClassLoader $composerInstance, callable $finderFactory, \ArrayAccess $factoryRepository)
    {
        $this->composerInstance = $composerInstance;

        $this->factoryRepository = $factoryRepository;
        $this->finderFactory = $finderFactory;

        if (class_exists('\Phar', false)) {
            //instructs phar to intercept fopen, file_get_contents, opendir, and all of the stat-related functions
            //Needed to support Phar with the loader
            \Phar::interceptFileFuncs();
        }
    }

    /**
     * Return the factory used to create new finder for all new factory
     *
     * @return callable
     */
    public function getFinderFactory()
    {
        return $this->finderFactory;
    }

    /**
     * Return the factory repository passed to all factory loaded by this loader
     *
     * @return \ArrayAccess
     */
    public function getFactoryRepository()
    {
        return $this->factoryRepository;
    }

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations.
     * @api
     * @param string $namespace
     * @param string $path
     *
     * @return $this
     */
    public function registerNamespace(string $namespace, string $path): LoaderInterface
    {
        if ('\\' !== $namespace[strlen($namespace)-1]) {
            $namespace = ltrim($namespace, '\\').'\\';
        }

        $this->composerInstance->addPsr4($namespace, $path);

        return $this;
    }

    /**
     * To load the factory of the stated class and check if it's implementing the good interface
     * @param string $factoryClassName
     * @return bool
     */
    private function loadFactory(string &$factoryClassName): bool
    {
        if (!isset($this->factoryAvailabilityList[$factoryClassName])) {
            if (true === class_exists($factoryClassName, false)
                || true === $this->composerInstance->loadClass($factoryClassName)) {
                $reflectionClassInstance = new \ReflectionClass($factoryClassName);

                $this->factoryAvailabilityList[$factoryClassName] = $reflectionClassInstance->implementsInterface('UniAlteri\\States\\Factory\\FactoryInterface');
            } else {
                $this->factoryAvailabilityList[$factoryClassName] = false;
            }
        }

        return $this->factoryAvailabilityList[$factoryClassName];
    }

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
    public function loadClass(string $className): bool
    {
        if (isset($this->loadingFactoriesClassNameArray[$className])
            || 0 === strpos($className, 'UniAlteri\\States')) {
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
     * @param string $factoryClassName
     * @param string $statedClassName
     * @param string $path
     *
     * @return Factory\FactoryInterface
     *
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory(string $factoryClassName, string $statedClassName, string $path): Factory\FactoryInterface
    {
        //Check if the factory class is loaded
        if (!class_exists($factoryClassName, false)) {
            throw new Exception\UnavailableFactory(
                sprintf('The factory of %s is not available', $statedClassName)
            );
        }

        //Create a new instance of the factory
        $finderFactory = $this->finderFactory;
        $factoryObject = new $factoryClassName(
            $statedClassName,
            $finderFactory($statedClassName, $path),
            $this->factoryRepository
        );

        if (!$factoryObject instanceof Factory\FactoryInterface) {
            throw new Exception\IllegalFactory(
                sprintf('The factory of %s does not implement the interface', $statedClassName)
            );
        }

        return $factoryObject;
    }
}
