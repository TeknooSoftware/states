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

class LoaderStandard implements LoaderInterface
{

    /**
     * List of path to include for this loader
     * @var \ArrayObject
     */
    protected $_includedPathArray = null;

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
     * Initialize the loader object
     */
    public function __construct()
    {
        $this->_includedPathArray = new \ArrayObject();
        $this->_namespacesArray = new \ArrayObject();
        $this->_previousIncludedPathStack = new \SplStack();
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

        $this->_includedPathArray[$path] = $path;
        return $this;
    }

    /**
     * Register a location to find some classes of a namespace.
     * A namespace can has several locations
     * @param string $namespace
     * @param string $path
     * @return $this
     * @throws Exception\UnavailablePath if the path is not readable
     */
    public function registerNamespace($namespace, $path)
    {
        if (false === is_dir($path)) {
            throw new Exception\UnavailablePath('Error, the path "'.$path.'" is not available');
        }

        if (!isset($this->_namespacesArray[$namespace])) {
            $this->_namespacesArray[$namespace] = new \SplQueue();
        }

        $this->_namespacesArray[$namespace]->enqueue($path);
    }

    /**
     * Update included path before loading clas
     */
    protected function _updateIncludedPaths()
    {
        //Convert paths to string
        $newPaths = implode(PATH_SEPARATOR, $this->_includedPathArray->getArrayCopy());
        //Update path into PHP
        $oldIncludedPaths = set_include_path(get_include_path().PATH_SEPARATOR.$newPaths);
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
        set_include_path($oldIncludedPaths);
    }

    /**
     * Loaded a class into a namespace
     * @param string $class
     * @return bool
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
            $classFile = $path.DIRECTORY_SEPARATOR.$className.'.php';
            if (is_readable($classFile)) {
                include_once($classFile);

                if (class_exists($classFile, false)) {
                    return true;
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
     * @throws \Exception
     */
    public function loadClass($className)
    {
        if (class_exists($className, false)) {
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
                $classFile = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $className).'.php';
                if (is_readable($classFile)) {
                    include_once($classFile);

                    if (class_exists($className)) {
                        //Class found and loaded
                        $classLoaded = true;
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
}