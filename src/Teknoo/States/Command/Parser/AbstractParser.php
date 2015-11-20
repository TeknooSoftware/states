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

namespace Teknoo\States\Command\Parser;

use Gaufrette\Filesystem;

/**
 * Class AbstractParser
 * Abstract class Parser to parse a class.
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
abstract class AbstractParser
{
    /**
     * Filesystem object to manipulate file.
     *
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * Path of the current stated class.
     *
     * @var string
     */
    protected $statedClassPath;

    /**
     * Return the file system object from Gaufrette to.
     *
     * @return Filesystem
     */
    protected function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * Path of the current stated class to operate.
     *
     * @param Filesystem $fileSystem
     * @param string     $path
     */
    public function __construct($fileSystem, $path)
    {
        $this->fileSystem = $fileSystem;
        $this->statedClassPath = $path;
    }

    /**
     * Browse all available files in the current path.
     *
     * @return \ArrayObject
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function listFiles()
    {
        //Extracts all php files (No check class exists)
        $filesArray = new \ArrayObject();
        foreach ($this->getFileSystem()->keys() as $file) {
            switch ($file) {
                case '.';
                case '..';
                    break;
                default:
                    $filesArray[] = $file;
                    break;
            }
        }

        return $filesArray;
    }

    /**
     * Load a file, and return the reflection class of this file.
     *
     * @param string $file
     *
     * @return \ReflectionClass
     *
     * @throws Exception\UnReadablePath when the path is not readable
     * @throws Exception\ClassNotFound
     */
    public function loadFile($file)
    {
        if (!$this->getFileSystem()->has($file)) {
            throw new Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }

        //Extract class name
        $className = $this->extractClassWithNamespace($file);
        include_once $this->statedClassPath.DIRECTORY_SEPARATOR.$file;

        if (!class_exists($className, false)) {
            throw new Exception\ClassNotFound('The class '.$className.' was not found');
        }

        return new \ReflectionClass($className);
    }

    /**
     * Return the end of the class name (without namespace).
     *
     * @return string
     */
    protected function getClassNameFile()
    {
        $explodedPath = explode(DIRECTORY_SEPARATOR, $this->statedClassPath);

        return end($explodedPath).'.php';
    }

    /**
     * Return the name space define in a file.
     *
     * @param string $file
     *
     * @return string
     */
    protected function extractClassWithNamespace($file)
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
            } elseif ($nameSpaceTokenDetected) {
                if (T_NS_SEPARATOR == $token[0]) {
                    $nameSpace .= '\\';
                } elseif (T_STRING == $token[0] || T_NS_SEPARATOR == $token[0]) {
                    $nameSpace .= $token[1];
                } elseif ('' != trim($token[1])) {
                    $nameSpaceTokenDetected = false;
                }
                continue;
            }

            if (T_CLASS == $token[0]) {
                //next token is about the name of the class
                $classTokenDetected = true;
            } elseif ($classTokenDetected && T_STRING == $token[0]) {
                //Class name found
                $className = $token[1];
                break;
            }
        }

        return $nameSpace.'\\'.$className;
    }

    /**
     * Load a file and return its content.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function getFile($file)
    {
        $fileSystem = $this->getFileSystem();
        if (!$fileSystem->has($file)) {
            throw new Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }

        return $fileSystem->read($file);
    }
}
