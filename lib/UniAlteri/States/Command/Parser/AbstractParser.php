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

namespace UniAlteri\States\Command\Parser;

use \Gaufrette\Adapter;
use \Gaufrette\Filesystem;
use \UniAlteri\States\Command\Parser\Exception;

/**
 * Class AbstractParser
 * Abstract class Parser to parse a class
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
abstract class AbstractParser
{
    /**
     * Adapter to operate with file system
     * @var Adapter
     */
    protected $_adapter;

    /**
     * Filesystem object to manipulate file
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * Path of the current stated class
     * @var string
     */
    protected $_statedClassPath;

    /**
     * Return the file system object from Gaufrette to
     * @return Filesystem
     * @throws Exception\IllegalArgument when the FS adapter is not valid
     * @throws Exception\UnavailablePath when the path is not available
     */
    protected function _getFileSystem()
    {
        if (!$this->_fileSystem instanceof Filesystem) {
            if (!$this->_adapter instanceof Adapter) {
                throw new Exception\IllegalArgument('Error, the adapter is not valid');
            }

            $this->_fileSystem = new Filesystem($this->_adapter);

            if ($this->_adapter->isDirectory($this->_statedClassPath)) {
                throw new Exception\UnavailablePath('Error, the path '.$this->_statedClassPath.' is not available');
            }
        }

        return $this->_fileSystem;
    }

    /**
     * Path of the current stated class to operate
     * @param Adapter $adapter
     * @param string $path
     */
    public function __construct(Adapter $adapter, $path)
    {
        $this->_adapter = $adapter;
        $this->_statedClassPath = $path;
    }

    /**
     * Browse all available files in the current path
     * @return \ArrayObject
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function listFiles()
    {
        //Extracts all php files (No check class exists)
        $filesArray = new \ArrayObject();
        foreach($this->_getFileSystem()->keys() as $file) {
            switch ($file) {
                case '.';
                case '..';
                    break;
                default:
                    if (strlen($file) - 4 == strrpos($file, '.php')) {
                        $filesArray[] = $file;
                    }
                    break;
            }
        }

        return $filesArray;
    }

    /**
     * Load a file, and return the reflection class of this file
     * @param string $file
     * @return \ReflectionClass
     * @throws Exception\UnReadablePath when the path is not readable
     * @throws Exception\ClassNotFound
     */
    public function loadFile($file)
    {
        $path = $this->_statedClassPath.DIRECTORY_SEPARATOR.$file;
        if (!$this->_getFileSystem()->has($path)) {
            throw new Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }

        //Extract class name
        $className = $this->_extractClassWithNamespace($path);
        include_once($this->_getFileSystem());

        if (!class_exists($className, false)) {
            throw new Exception\ClassNotFound('The class '.$className.' was not found');
        }

        return new \ReflectionClass($className);
    }

    /**
     * Return the name space define in a file
     * @param string $file
     * @return string
     */
    protected function _extractClassWithNamespace($file)
    {
        $nameSpace = null;
        $className = null;

        //Fun lexers of Zend to get token of this file
        $tokens = token_get_all($this->getFile($file));
        $classTokenDetected = false;
        $nameSpaceTokenDetected = false;
        //Browse tokens to find the class name
        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }

            if (T_NAMESPACE == $token[0]) {
                //next token is about the name of the class
                $nameSpaceTokenDetected = true;
            } else if ($nameSpaceTokenDetected) {
                if (T_NS_SEPARATOR == $token[0]) {
                    $nameSpace .= '\\';
                } elseif (T_STRING) {
                    $nameSpace .= $token[0];
                } else {
                    $nameSpaceTokenDetected = false;
                }
                break;
            }

            if (T_CLASS == $token[0]) {
                //next token is about the name of the class
                $classTokenDetected = true;
            } else if ($classTokenDetected && T_STRING == $token[0]) {
                //Class name found
                $className = $token[1];
                break;
            }
        }

        return $nameSpace.'\\'.$className;
    }

    /**
     * Load a file and return its content
     * @param string $file
     * @return string
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function getFile($file)
    {
        $fileSystem = $this->_getFileSystem();
        if (!$fileSystem->has($this->_statedClassPath.DIRECTORY_SEPARATOR.$file)) {
            throw new Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }

        return $fileSystem->read($this->_statedClassPath.DIRECTORY_SEPARATOR.$file);
    }
}