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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\States\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Class ClassCreate
 * Command to create a new empty stated class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class AbstractCommand extends Command
{
    /**
     * @var callable
     */
    protected $factory;

    /**
     * @var callable
     */
    protected $fileSystemFactory;

    /**
     * @param string   $name
     * @param callable $factory
     * @param callable $fileSystemFactory
     */
    public function __construct($name = null, callable $factory = null, callable $fileSystemFactory = null)
    {
        $this->factory = $factory;
        $this->fileSystemFactory = $fileSystemFactory;
        parent::__construct($name);
    }

    /**
     * Return parser/writer factory user by this command.
     *
     * @return callable|null
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return the file system factory used by this command.
     *
     * @return callable|null
     */
    public function getFileSystemFactory()
    {
        return $this->fileSystemFactory;
    }

    /**
     * Create a parser from the factory.
     *
     * @param string $name
     * @param string $destinationPath
     *
     * @return Parser\Factory|Parser\Proxy|Parser\State|Parser\StatedClass
     */
    public function createParser($name, $destinationPath)
    {
        $factory = $this->factory;

        return $factory($name, $destinationPath);
    }

    /**
     * Create a writer from the factory.
     *
     * @param string $name
     * @param string $destinationPath
     *
     * @return Writer\Factory|Writer\Proxy|Writer\State
     */
    public function createWriter($name, $destinationPath)
    {
        $factory = $this->factory;

        return $factory($name, $destinationPath);
    }
}