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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\DI\Exception;

class VirtualDIContainer implements DI\ContainerInterface
{
    /**
     * @var array
     */
    protected $_instance = array();

    /**
     * @var array
     */
    protected $_service = array();

    /**
     * To support object cloning : All registry must be cloning, but not theirs values
     */
    public function __clone()
    {

    }

    /**
     * Call an entry of the container to retrieve an instance
     *
     * @param string $name : identifier of the instance
     * @return mixed
     * @throws Exception\InvalidArgument if the identifier is not defined
     */
    public function get($name)
    {
        if (isset($this->_instance[$name])) {
            return $this->_instance[$name];
        }

        if (isset($this->_service[$name])) {
            return $this->_service[$name]($this);
        }

        return null;
    }

    /**
     * Register a new shared object into container (the same object is returned at each call)
     * @param string $name
     * @param object|callable|string $instance
     * @return $this
     * @throws Exception\ClassNotFound if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     */
    public function registerInstance($name, $instance)
    {
        $this->_instance[$name] = $instance;
    }

    /**
     * Register a new service into container (a new instance is returned at each call)
     * @param string $name : interface name, class name, alias
     * @param object|callable|string $instance
     * @return string unique identifier of the object
     * @throws Exception\ClassNotFound if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     */
    public function registerService($name, $instance)
    {
        $this->_service[$name] = $instance;
    }

    /**
     * Test if an entry is already registered
     * @param string $name
     * @return boolean
     */
    public function testEntry($name)
    {
        return isset($this->_instance[$name]) || isset($this->_service[$name]);
    }

    /**
     * Remove an entry from the container
     * @param string $name
     * @return $this
     */
    public function unregister($name)
    {
        if (isset($this->_instance[$name])) {
            unset($this->_instance[$name]);
        }

        if (isset($this->_service[$name])) {
            unset($this->_service[$name]);
        }

        return $this;
    }

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param array|\ArrayObject $params
     * @return mixed
     */
    public function configure($params)
    {

    }

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions()
    {

    }
}