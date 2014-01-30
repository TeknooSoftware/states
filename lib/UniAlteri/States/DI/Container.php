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
 * @project     States
 * @category    DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\DI;

class Container extends \Pimple implements ContainerInterface
{
    /**
     * To support object cloning : All registry must be cloning, but not theirs values
     */
    public function __clone()
    {
        /**
         * Do nothing, Pimple use standard array, they are automatically cloned by php (but not theirs values)
         */
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
        try {
            return $this[$name];
        } catch (\InvalidArgumentException $e) {
            throw new Exception\InvalidArgument($e->getMessage(), $e->getCode(), $e);
        }
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
        if (\is_string($instance)) {
            //Load the class and build a new object of this class
            if (\class_exists($instance)) {
                $this[$name] = new $instance();
            } else {
                throw new Exception\ClassNotFound('The class "'.$instance.'" is not available');
            }
        } elseif (is_object($instance) || is_callable($instance)) {
            //For callable and object, register them
            $this[$name] = $instance;
        } else {
            throw new Exception\IllegalService('Error, the instance for "'.$name.'" is illegal');
        }

        return $this;
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
        if (\is_string($instance)) {
            //Class, check if it is loaded
            if (\class_exists($instance)) {
                //Write a new closure to build a new instance of this class, and use it as service
                $this[$name] = $this->factory(function($c) use($instance){
                    return new $instance($c);
                });
            } else {
                throw new Exception\ClassNotFound('The class "'.$instance.'" is not available');
            }
        } elseif (\is_object($instance)) {
            //Add the object as service into container
            if (!\method_exists($instance, '__invoke')) {
                $this[$name] = $this->factory($instance);
            } else {
                throw new Exception\IllegalService('Error, the service for "'.$name.'" is not an invokable object');
            }
        } elseif(\is_callable($instance)) {
            //Add closure as service
            $this[$name] = $this->factory($instance);
        } else {
            throw new Exception\IllegalService('Error, the service for "'.$name.'" is illegal');
        }
    }

    /**
     * Test if an entry is already registered
     * @param string $name
     * @return boolean
     */
    public function testEntry($name)
    {
        return isset($this[$name]);
    }

    /**
     * Remove an entry from the container
     * @param string $name
     */
    public function unregister($name)
    {
        unset($this[$name]);
    }

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param array|\ArrayObject $params
     * @return mixed
     */
    public function configure($params)
    {
        if (isset($params['services'])) {
            foreach ($params['services'] as $name => $instance) {
                $this->registerService($name, $instance);
            }
        }

        if (isset($params['instances'])) {
            foreach($params['instances'] as $name => $instance) {
                $this->registerInstance($name, $instance);
            }
        }
    }

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions()
    {
        return $this->keys();
    }
}