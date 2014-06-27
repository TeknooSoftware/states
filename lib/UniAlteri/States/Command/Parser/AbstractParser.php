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

use \UniAlteri\States\Command;

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
     * Path of the current stated class
     * @var string
     */
    protected $_statedClassPath;

    /**
     * Path of the current stated class to operate
     * @param string $path
     * @throws Command\Exception\UnavailablePath when the path is not available
     */
    public function __construct($path)
    {
        $this->_statedClassPath = $path;

        if (!is_dir($this->_statedClassPath)) {
            throw new Command\Exception\UnavailablePath('Error, the path '.$this->_statedClassPath.' is not available');
        }
    }

    /**
     * Browse all available files in the current path
     * @return \ArrayObject
     * @throws Command\Exception\UnReadablePath when the path is not readable
     */
    public function listFiles()
    {
        //Checks if the path is available
        $hD = @opendir($this->_statedClassPath);
        if (false === $hD) {
            throw new Command\Exception\UnReadablePath('Error, the path "'.$this->_statedClassPath.'" is not available');
        }

        //Extracts all php files (No check class exists)
        $filesArray = new \ArrayObject();
        while (false !== ($file = readdir($hD))) {
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

        closedir($hD);
        return $filesArray;
    }

    /**
     * Load a file, and return the reflection class of this file
     * @param string $file
     * @return \ReflectionClass
     * @throws Command\Exception\UnReadablePath when the path is not readable
     */
    public function loadFile($file)
    {
        if (!is_readable($this->_statedClassPath.DIRECTORY_SEPARATOR.$file)) {
            throw new Command\Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }
    }

    /**
     * Load a file and return its content
     * @param string $file
     * @return string
     * @throws Command\Exception\UnReadablePath when the path is not readable
     */
    public function getFile($file)
    {
        if (!is_readable($this->_statedClassPath.DIRECTORY_SEPARATOR.$file)) {
            throw new Command\Exception\UnReadablePath('Error, the file '.$file.' is not readable');
        }

        return file_get_contents($this->_statedClassPath.DIRECTORY_SEPARATOR.$file);
    }
}