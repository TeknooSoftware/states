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

namespace UniAlteri\States\Command\Writer;

use Gaufrette\Filesystem;
use spec\Gaufrette\Adapter;
use \UniAlteri\States\Command\Writer\Exception;

/**
 * Class Writer
 * Abstract class Writer to create, update or delete a file
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
abstract class AbstractWriter
{
    /**
     * Adapter to operate with file system
     * @var callable
     */
    protected $adapterFactory;

    /**
     * Filesystem object to manipulate file
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * Path of the current stated class
     * @var string
     */
    protected $statedClassPath;

    /**
     * Return the file system object from Gaufrette to
     * @return Filesystem
     * @throws Exception\IllegalArgument when the FS adapter is not valid
     * @throws Exception\UnavailablePath when the path is not available
     */
    protected function getFileSystem()
    {
        if (!$this->fileSystem instanceof Filesystem) {
            if (!is_callable($this->adapterFactory)) {
                throw new Exception\IllegalArgument('Error, the adapter factory is not valid');
            }

            $adapter = call_user_func_array($this->adapterFactory, array($this->statedClassPath));
            $this->fileSystem = new Filesystem($adapter);

            if (!$adapter->isDirectory('/')) {
                throw new Exception\UnavailablePath('Error, the path '.$this->statedClassPath.' is not available');
            }
        }

        return $this->fileSystem;
    }

    /**
     * Path of the current stated class to operate
     * @param callable $adapterFactory
     * @param string $path
     */
    public function __construct($adapterFactory, $path)
    {
        $this->adapterFactory = $adapterFactory;
        $this->statedClassPath = $path;
    }

    /**
     * Create or replace a file with a content
     * @param string $file
     * @param string $content
     * @return int
     */
    protected function write($file, $content)
    {
        return $this->getFileSystem()->write($file, $content, true);
    }

    /**
     * Method to delete a file
     * @param string $file
     * @return boolean
     */
    protected function _delete($file)
    {
        return $this->getFileSystem()->delete($file);
    }
}