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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.0
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\DI\Exception;

/**
 * Class MockDIContainer
 * Mock DI Container to unit testing different elements of this libs
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockDIContainer implements DI\ContainerInterface
{
    /**
     * Registered instance in this container
     * @var array
     */
    protected $instance = array();

    /**
     * Registered service in this container
     * @var array
     */
    protected $service = array();

    /**
     * To support object cloning : All registry must be cloning, but not theirs values
     */
    public function __clone()
    {
        //Do nothing, array are already cloned, but not theirs values by PHP
    }

    /**
     * Call an entry of the container to retrieve an instance or a service
     *
     * @param  string                    $name : identifier of the instance
     * @return mixed
     * @throws Exception\InvalidArgument if the identifier is not defined
     */
    public function get($name)
    {
        if (isset($this->instance[$name])) {
            //It is an instance, return it
            return $this->instance[$name];
        }

        if (isset($this->service[$name])) {
            //It is a service, call it and return the result
            return $this->service[$name]($this);
        }

        return null;
    }

    /**
     * Register a new shared object into container (the same object is returned at each call)
     * @param  string                   $name
     * @param  object|callable|string   $instance
     * @return $this
     * @throws Exception\ClassNotFound  if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     */
    public function registerInstance($name, $instance)
    {
        $this->instance[$name] = $instance;
    }

    /**
     * Register a new service into container (a new instance is returned at each call)
     * @param  string                   $name     : interface name, class name, alias
     * @param  object|callable|string   $instance
     * @return string                   unique identifier of the object
     * @throws Exception\ClassNotFound  if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     */
    public function registerService($name, $instance)
    {
        $this->service[$name] = $instance;
    }

    /**
     * Test if an entry is already registered
     * @param  string  $name
     * @return boolean
     */
    public function testEntry($name)
    {
        return isset($this->instance[$name]) || isset($this->service[$name]);
    }

    /**
     * Remove an entry from the container
     * @param  string $name
     * @return $this
     */
    public function unregister($name)
    {
        if (isset($this->instance[$name])) {
            unset($this->instance[$name]);
        }

        if (isset($this->service[$name])) {
            unset($this->service[$name]);
        }

        return $this;
    }

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param  array|\ArrayObject $params
     * @return mixed
     */
    public function configure($params)
    {
        //Not used to test others elements
    }

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions()
    {
        //Not used to test others elements
    }
}
