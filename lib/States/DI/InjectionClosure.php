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

class InjectionClosure implements InjectionClosureInterface
{

    /**
     * DI Container to use with this closure
     * @var ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Closure hosted by this object
     * @var \Closure
     */
    protected $_closure = null;

    /**
     * All statics properties
     * @var \ArrayObject
     */
    protected $_properties = null;

    /**
     * To create a closure for DI Container with a support of persistent vars. (aka "static")
     */
    public function __construct(){;
        $this->_properties = new \ArrayObject();
    }

    /**
     * Register a DI container for this object
     * @param ContainerInterface $container
     */
    public function setDIContainer(ContainerInterface $container){
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return ContainerInterface
     */
    public function getDIContainer(){
        return $this->_diContainer;
    }

    /**
     * Execute the closure
     * @return mixed
     */
    public function __invoke(){
        return \call_user_func_array($this->_closure, \func_get_args());
    }

    /**
     * Return the closure contained into this
     * @param \Closure $closure
     * @return $this
     */
    public function setClosure(\Closure $closure){
        $this->_closure = $closure;

        return $this;
    }

    /**
     * Return the closure contained into this
     * @return \Closure
     */
    public function getClosure(){
        return $this->_closure;
    }

    /**
     * Test if the name of the static property is valid
     * @param string $name
     * @return boolean
     * @throws Exception\IllegalName
     */
    protected function _validatingName($name){
        if(0 == preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#iS', $name)){
            throw new Exception\IllegalName('Illegal name for static property "'.$name.'"');
        }

        return true;
    }

    /**
     * To allow the closure to save a static property,
     * to allow developer to not use "static" key word into the closure
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function saveProperty($name, $value){
        if(true === $this->_validatingName($name)){
            $this->_properties->{$name} = $value;
        }

        return $this;
    }

    /**
     * Remove a static property
     * @param string $name
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function deleteProperty($name){
        if(true === $this->_validatingName($name) && isset($this->_properties->{$name})){
            unset($this->_properties->{$name});
        }

        return $this;
    }

    /**
     * Return to the closure a static property
     * @param string $name
     * @return mixed
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function getProperty($name){
        if(true === $this->_validatingName($name) && isset($this->_properties->{$name})){
            return $this->_properties->{$name};
        }

        return null;
    }

    /**
     * Check if a static property is stored
     * @param string $name
     * @return boolean
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function testProperty($name){
        return true === $this->_validatingName($name) && isset($this->_properties->{$name});
    }
}