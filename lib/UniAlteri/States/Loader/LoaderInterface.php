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

/**
 * Interface LoaderInterface
 * @package UniAlteri\States\Loader
 * Interface to define a "stated class autoloader" to allow php to load stated class
 */
interface LoaderInterface
{
    /**
     * PHP File of Factory into each stated class
     */
    const FACTORY_FILE_NAME = 'Factory.php';

    /**
     * Suffix name of the Factory PHP Class of each Stated Class (The pattern is <statedClassName>[Suffix]
     */
    const FACTORY_CLASS_NAME = 'Factory';

    /**
     * Method to add a path on the list of location where find class
     * @param string $path
     * @return $this
     * @throws Exception\UnavailablePath if the path is not readable
     */
    public function addIncludePath($path);

    /**
     * Register a location to find some classes of a namespace.
     * A namespace can has several locations
     * @param string $namespace
     * @param string $path
     * @return $this
     * @throws Exception\UnavailablePath if the path is not readable
     */
    public function registerNamespace($namespace, $path);

    /**
     * Method called to load a class.
     * @param string $className class name, support namespace prefixes
     * @return boolean
     * @throws Exception\EmptyStack if the stack of previous included path
     * @throws \Exception
     */
    public function loadClass($className);
}