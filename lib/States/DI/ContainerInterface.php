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
 * to license@centurion-project.org so we can send you a copy immediately.
 *
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\DI;

interface ContainerInterface{

    /**
     * To support object cloning
     */
    public function __clone();

    /**
     * Call an entry of the container to retrieve an instance
     * @param string $name : interface name, class name, alias
     * @param array $params : params to build a new instance
     * @return mixed
     */
    public function get($name);

    /**
     * Return a new instance of the $name
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function registerInstance($name, $instance);

    /**
     * @param string $name : interface name, class name, alias
     * @param object|callable|string $instance
     * @return string unique identifier of the object
     */
    public function registerService($name, $instance);

    /**
     * Test if an instance is already registered
     * @param string $name
     * @return boolean
     */
    public function testInstance($name);

    /**
     * Remove an entry from the container
     * @param string $name
     */
    public function unregister($name);

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param array|ArrayObject $params
     * @return mixed
     */
    public function configure($params);

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions();
}