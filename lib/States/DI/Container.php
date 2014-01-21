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

class Container extends \Pimple implements ContainerInterface{

    /**
     * To support object cloning
     */
    public function __clone(){
        /**
         * Do nothing, Pimple use standard array, they are automatically cloned by php (but not theirs values)
         */
    }

    /**
     * Call an entry of the container to retrieve an instance
     * @param string $name : interface name, class name, alias
     * @param array $params : params to build a new instance
     * @return mixed
     */
    public function get($name){
        return $this[$name];
    }

    /**
     * Return always the same instance of class
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function registerInstance($name, $instance){
        if(is_string($instance)){
            //Load the class and buid a new object of this class
            if(class_exists($instance)){
                $this[$name] = new $instance();
            }
            else{
                throw new \UniAlteri\States\Exception\ClassNotFound('The class "'.$instance.'" is not available');
            }
        }
        else{
            //For callable and object, regiester them
            $this[$name] = $instance;
        }
    }

    /**
     * @param string $name : interface name, class name, alias
     * @param object|callable|string $instance
     * @return string unique identifier of the object
     */
    public function registerService($name, $instance){
        if(is_string($instance)){
            //Class, check if it is loaded
            if(class_exists($instance)){
                //Write a new closure to build a new instance of this class, and use it as service
                $this[$name] = $this->factory(function($c) use($instance){
                    return new $instance($c);
                });
            }
            else{
                throw new \UniAlteri\States\Exception\ClassNotFound('The class "'.$instance.'" is not available');
            }
        }
        elseif(is_object($instance)){
            //Add the object as service into container
            if(!method_exists($instance, '__invoke')){
                $this[$name] = $this->factory($instance);
            }
            else{
                throw new \UniAlteri\States\Exception\IllegalService('Error, the service for "'.$name.'" is not an invokable object');
            }
        }
        elseif(is_callable($instance)){
            //Add closure as service
            $this[$name] = $this->factory($instance);
        }
        else{
            throw new \UniAlteri\States\Exception\IllegalService('Error, the service for "'.$name.'" is illegal');
        }
    }

    /**
     * Test if an instance is already registered
     * @param string $name
     * @return boolean
     */
    public function testInstance($name){
        return isset($this[$name]);
    }

    /**
     * Remove an entry from the container
     * @param string $name
     */
    public function unregister($name){
        unset($this[$name]);
    }

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param array|ArrayObject $params
     * @return mixed
     */
    public function configure($params){
        if(isset($params['shared'])){
            foreach($params['shared'] as $name=>$instance){
                $this->shareInstance($name, $instance);
            }
        }

        if(isset($params['instances'])){
            foreach($params['instances'] as $name=>$instance){
                $this->registerInstance($name, $instance);
            }
        }
    }

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions(){
        return $this->keys();
    }
}