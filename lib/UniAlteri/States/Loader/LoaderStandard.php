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
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\States\Loader;

use \UniAlteri\States\DI;
use \UniAlteri\States\Factory;

/**
 * Class LoaderStandard
 * Default implementation of the "stated class autoloader".
 * It is used to allow php to load automatically stated class.
 *
 * @package     States
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @api
 */
class LoaderStandard implements LoaderInterface
{
    /**
     * DI Container to use with this loader
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * List of paths to include for this loader
     * @var \ArrayObject
     */
    protected $_includedPathsArray = null;

    /**
     * List of paths where namespace are available
     * @var \SplQueue[]
     */
    protected $_namespacesArray = null;

    /**
     * Backup of previous included path configuration
     * @var \SplStack
     */
    protected $_previousIncludedPathStack = null;

    /**
     * Manager to use by this loader to manipulate include path
     * @var IncludePathManagerInterface
     */
    protected $_includePathManager = null;

    /**
     * Initialize the loader object
     * @param  IncludePathManagerInterface $includePathManager
     * @throws Exception\IllegalArgument   $includePathManager does not implement the interface IncludePathManagerInterface
     */
    public function __construct($includePathManager)
    {
        if (!$includePathManager instanceof IncludePathManagerInterface) {
            throw new Exception\IllegalArgument(
                'Error, the include path manager does not implement the interface IncludePathManagerInterface'
            );
        }

        //Initialize the object
        $this->_includedPathsArray = new \ArrayObject();
        $this->_namespacesArray = new \ArrayObject();
        $this->_previousIncludedPathStack = new \SplStack();
        $this->_includePathManager = $includePathManager;

        if (class_exists('\Phar', false)) {
            //instructs phar to intercept fopen, file_get_contents, opendir, and all of the stat-related functions
            \Phar::interceptFileFuncs();
        }
    }

    /**
     * Return the current include path manager
     * @return IncludePathManagerInterface
     */
    protected function _getIncludePathManager()
    {
        return $this->_includePathManager;
    }

    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * Method to add a path on the list of location where find class
     * @param  string                    $path
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
     * To list all active included path for this loaded
     * @return string[]
     */
    public function getIncludedPaths()
    {
        return $this->_includedPathsArray;
    }

    /**
     * To register a location to find some classes of a namespace.
     * A namespace can has several locations
     * @param  string                    $namespace
     * @param  string                    $path
     * @return $this
     * @throws Exception\IllegalArgument if the path is not a valid string
     */
    public function registerNamespace($namespace, $path)
    {
        if (!is_string($path)) {
            throw new Exception\IllegalArgument('Error, the path is not a valid string');
        }

        //Prepend the namespace with "\" to avoid mismatch error
        if ('\\' != $namespace[0]) {
            $namespace = '\\'.$namespace;
        }

        //Initialize the stack of paths for this namespace
        if (!isset($this->_namespacesArray[$namespace])) {
            $this->_namespacesArray[$namespace] = new \SplQueue();
        }

        if ('/' == $path[strlen($path)-1]) {
            $path = substr($path, 0, strlen($path)-1);
        }

        $this->_namespacesArray[$namespace]->enqueue($path);
    }

    /**
     * To list all registered namespace
     * @return \ArrayObject
     */
    public function listNamespaces()
    {
        return $this->_namespacesArray;
    }

    /**
     * To update included path before loading class
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
     * To restore previous loaded class
     */
    protected function _restoreIncludedPaths()
    {
        if (!$this->_previousIncludedPathStack->isEmpty()) {
            $oldIncludedPaths = $this->_previousIncludedPathStack->pop();
            $this->_getIncludePathManager()->setIncludePath($oldIncludedPaths);
        }
    }

    /**
     * To load a class into a namespace
     * @param  string                       $class
     * @return bool
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
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

        if (end($namespacePartsArray) === $className) {
            //Prevent when developer call directly the proxy class name
            array_pop($namespacePartsArray);
            $class = implode('\\', $namespacePartsArray).'\\'.$className;
        }

        //Rebuild namespace
        $namespaceString = '\\'.implode('\\', $namespacePartsArray);
        if (!isset($this->_namespacesArray[$namespaceString])) {
            return false;
        }

        //Browse each
        $result = false;
        foreach ($this->_namespacesArray[$namespaceString] as $path) {
            //Compute the factory file of the stated class
            $factoryFile = $path.DIRECTORY_SEPARATOR.$className.DIRECTORY_SEPARATOR.LoaderInterface::FACTORY_FILE_NAME;
            if (is_readable($factoryFile)) {
                //Factory found, load it
                include_once($factoryFile);

                //Compute factory class name with its namespace
                $factoryClassName = $namespaceString.'\\'.$className.'\\'.LoaderInterface::FACTORY_CLASS_NAME;
                if (class_exists($factoryClassName, false)) {
                    //Initialize the factory
                    try {
                        $this->buildFactory($factoryClassName, $class, $path.DIRECTORY_SEPARATOR.$className);
                        $result = true;
                    } catch (\Exception $e) {
                        $result = false;
                    }
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * To test if a file exists, check also in all included path
     * @param  string  $pathFile
     * @return boolean
     */
    protected function _testFileExist($pathFile)
    {
        foreach ($this->_getIncludePathManager()->getIncludePath() as $includedPath) {
            if (is_readable($includedPath.DIRECTORY_SEPARATOR.$pathFile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method called to load a class by __autoload of PHP Engine
     * @param  string                       $className class name, support namespace prefixes
     * @return boolean
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     * @throws \Exception
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
                if (DIRECTORY_SEPARATOR == $path[0]) {
                    $path = substr($path, 1);
                }
                //Compute the factory file of the stated class
                $factoryClassFile = $path.DIRECTORY_SEPARATOR.LoaderInterface::FACTORY_FILE_NAME;
                if ($this->_testFileExist($factoryClassFile)) {
                    //Factory found, load it
                    include_once($factoryClassFile);

                    if (class_exists($factoryClassName, false)) {
                        //Class found and loaded
                        try {
                            //Initialize the factory
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
        } catch (\Exception $e) {
            $this->_restoreIncludedPaths();
            throw $e;
        }

        $this->_restoreIncludedPaths();

        return $classLoaded;
    }

    /**
     * Build the factory and initialize the loading stated class
     * @param  string                       $factoryClassName
     * @param  string                       $statedClassName
     * @param  string                       $path
     * @return Factory\FactoryInterface
     * @throws Exception\UnavailableFactory if the required factory is not available
     * @throws Exception\IllegalFactory     if the factory does not implement the good interface
     */
    public function buildFactory($factoryClassName, $statedClassName, $path)
    {
        //Check if the factory class is loaded
        if (!class_exists($factoryClassName, false)) {
            throw new Exception\UnavailableFactory(
                'The factory of '.$statedClassName.' is not available'
            );
        }

        //Create a new instance of the factory
        $factoryObject = new $factoryClassName();
        if (!$factoryObject instanceof Factory\FactoryInterface) {
            throw new Exception\IllegalFactory(
                'The factory of '.$statedClassName.' does not implement the interface'
            );
        }

        //clone the di container for this stated class, it will has its own di container
        if ($this->_diContainer instanceof DI\ContainerInterface) {
            $diContainer = clone $this->_diContainer;
            $factoryObject->setDIContainer($diContainer);
        }

        //Call its initialize methods to load the stated class
        $factoryObject->initialize($statedClassName, $path);

        return $factoryObject;
    }
}
