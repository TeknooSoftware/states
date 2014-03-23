<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;

use \UniAlteri\States\DI;
use \UniAlteri\States\Factory;

/**
 * Class LoaderStandard
 * @package UniAlteri\States\Loader
 * Default implementation of the "stated class autoloader".
 * It is used to allow php to load stated class
 */
class LoaderStandard implements LoaderInterface
{
    /**
     * DI Container to use with this finder
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * List of path to include for this loader
     * @var \ArrayObject
     */
    protected $_includedPathsArray = null;

    /**
     * List of path where namespace are available
     * @var \SplQueue[]
     */
    protected $_namespacesArray = null;

    /**
     * Backup of previous included path configuretion
     * @var \SplStack
     */
    protected $_previousIncludedPathStack = null;

    /**
     * @var IncludePathManagementInterface
     */
    protected $_includePathManager = null;

    /**
     * Initialize the loader object
     * @param IncludePathManagementInterface $includePathManager
     * @throws Exception\IllegalArgument $includePathManager does not implement the interface  IncludePathManagementInterface
     */
    public function __construct(IncludePathManagementInterface $includePathManager)
    {
        if (!$includePathManager instanceof IncludePathManagementInterface) {
            throw new Exception\IllegalArgument('Error, the include path manager does not implement the interface IncludePathManagementInterface');
        }

        $this->_includedPathsArray = new \ArrayObject();
        $this->_namespacesArray = new \ArrayObject();
        $this->_previousIncludedPathStack = new \SplStack();
        $this->_includePathManager = $includePathManager;
    }

    /**
     * Return the current include path managger
     * @return IncludePathManagementInterface
     */
    protected function _getIncludePathManager()
    {
        return $this->_includePathManager;
    }

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;
        return $this;
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * Method to add a path on the list of location where find class
     * @param string $path
     * @return $this
     * @throws Exception\UnavailablePath if the path is not readable
     */
    public function addIncludePath($path)
    {
        if (false === is_dir($path)) {
            throw new Exception\UnavailablePath('Error, the path "'.$path.'" is not available');
        }

        $this->_includedPathsArray[$path] = $path;
        return $this;
    }

    /**
     * List all active included path for this loaded
     * @return string[]
     */
    public function getIncludedPaths()
    {
        return $this->_includedPathsArray;
    }

    /**
     * Register a location to find some classes of a namespace.
     * A namespace can has several locations
     * @param string $namespace
     * @param string $path
     * @return $this
     * @throws Exception\IllegalArgument if the path is not a valid string
     */
    public function registerNamespace($namespace, $path)
    {
        if (!is_string($path)) {
            throw new Exception\IllegalArgument('Error, the path is not a valid string');
        }

        if (!isset($this->_namespacesArray[$namespace])) {
            $this->_namespacesArray[$namespace] = new \SplQueue();
        }

        $this->_namespacesArray[$namespace]->enqueue($path);
    }

    /**
     * List all registered namespace
     * @return \ArrayObject
     */
    public function listNamespaces()
    {
        return $this->_namespacesArray;
    }

    /**
     * Update included path before loading class
     */
    protected function _updateIncludedPaths()
    {
        //Convert paths to string
        //Update path into PHP
        $oldIncludedPaths = $this->_getIncludePathManager()->setIncludePath(
            array_merge(
                $this->_getIncludePathManager()->getIncludePath(),
                array_values($this->_includedPathsArray->getArrayCopy())
            )
        );
        //Store previous path to restore them
        $this->_previousIncludedPathStack->push($oldIncludedPaths);
    }

    /**
     * Restore previous loaded class
     */
    protected function _restoreIncludedPaths()
    {
        if ($this->_previousIncludedPathStack->isEmpty()) {
            throw new Exception\EmptyStack('Error, the stack of previous included path is empty');
        }

        $oldIncludedPaths = $this->_previousIncludedPathStack->pop();
        $this->_getIncludePathManager()->setIncludePath($oldIncludedPaths);
    }

    /**
     * Loaded a class into a namespace
     * @param string $class
     * @return bool
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory if the factory does not implement the good interface
     * @todo test
     */
    protected function _loadNamespaceClass($class)
    {
        $namespacePartsArray = explode('\\', $class);

        if (1 == count($namespacePartsArray)) {
            //No namespace, default to basic behavior
            return false;
        }

        $className = array_pop($namespacePartsArray);
        if ('' == $namespacePartsArray[0]) {
            //Prevent '\' at start
            array_shift($namespacePartsArray);
        }

        //Rebuild namespace
        $namespaceString = '\\'.implode('\\', $namespacePartsArray);
        if (!isset($this->_namespacesArray[$namespaceString])) {
            return false;
        }

        //Browse each
        foreach ($this->_namespacesArray[$namespaceString] as $path) {
            $factoryFile = $path.DIRECTORY_SEPARATOR.$className.DIRECTORY_SEPARATOR.LoaderInterface::FACTORY_FILE_NAME;
            if (is_readable($factoryFile)) {
                include_once($factoryFile);

                $factoryClassName = $namespaceString.'\\'.$className.'\\'.LoaderInterface::FACTORY_CLASS_NAME;
                if (class_exists($factoryClassName, false)) {
                    try {
                        $this->buildFactory($factoryClassName, $class, $path.DIRECTORY_SEPARATOR.$className);
                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Method called to load a class.
     * @param string $className class name, support namespace prefixes
     * @return boolean
     * @throws Exception\EmptyStack if the stack of previous included path
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory if the factory does not implement the good interface
     * @throws \Exception
     * @todo test
     */
    public function loadClass($className)
    {
        $factoryClassName = $className.'\\'.LoaderInterface::FACTORY_CLASS_NAME;
        if (class_exists($factoryClassName, false)) {
            //Prevent class already loaded
            return true;
        }

        //Update included path
        $this->_updateIncludedPaths();
        $classLoaded = false;

        try {
            //If the namespace is configured, check its paths
            if (false === $this->_loadNamespaceClass($className)) {
                //Class not found, switch to basic mode, replace \ and _ by a directory separator
                $path = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $className);
                $factoryClassFile = $path.DIRECTORY_SEPARATOR.LoaderInterface::FACTORY_FILE_NAME;
                if (is_readable($factoryClassFile)) {
                    include_once($factoryClassFile);

                    if (class_exists($factoryClassName, false)) {
                        //Class found and loaded
                        try {
                            $this->buildFactory($factoryClassName, $className, $path);
                            $classLoaded = true;
                        } catch (\Exception $e) {
                            $classLoaded = false;
                        }
                    }
                }
            } else {
                $classLoaded = true;
            }
        } catch(\Exception $e) {
            $this->_restoreIncludedPaths();
            throw $e;
        }

        $this->_restoreIncludedPaths();
        return $classLoaded;
    }

    /**
     * Build the factory and initialize the loading stated class
     * @param string $factoryClassName
     * @param string $statedClassName
     * @param string $path
     * @return Factory\FactoryInterface
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory if the factory does not implement the good interface
     */
    public function buildFactory($factoryClassName, $statedClassName, $path)
    {
        if (!class_exists($factoryClassName, false)) {
            throw new Exception\UnavailableFactory(
                'The factory of '.$statedClassName.' is not available'
            );
        }

        $factoryObject = new $factoryClassName();
        if (!$factoryObject instanceof Factory\FactoryInterface) {
            throw new Exception\IllegalFactory(
                'The factory of '.$statedClassName.' does not implement the interface'
            );
        }

        if ($this->_diContainer instanceof DI\ContainerInterface) {
            $diContainer = clone $this->_diContainer;
            $factoryObject->setDIContainer($diContainer);
        }

        $factoryObject->initialize($statedClassName, $path);
        return $factoryObject;
    }
}