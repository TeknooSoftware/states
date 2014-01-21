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

class InjectionClosure implements InjectionClosureInterface{

    /**
     * @var \UniAlteri\States\DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * @var \Closure
     */
    protected $_closure = null;

    /**
     * @var \ArrayObject
     */
    protected $_properties = null;

    /**
     * To create a closure for DI Container with a support of persistant vars. (aka "static")
     */
    public function __construct(){;
        $this->_properties = new \ArrayObject();
    }

    /**
     * Execute the closure
     * @return mixed
     */
    public function __invoke(){
        return \call_user_func_array($this->_closure, \func_get_args());
    }

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(\UniAlteri\States\DI\ContainerInterface $container){
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_diContainer;
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
     * @return bool
     * @throws \UniAlteri\States\Exception\IllegalName
     */
    protected function _validatingName($name){
        if(0 == preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#iS', $name)){
            throw new \UniAlteri\States\Exception\IllegalName('Illegal name for static property "'.$name.'"');
        }

        return true;
    }

    /**
     * To allow the closure to save a static property, to allow developer to not use "static" key word into the closure
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function saveStaticProperty($name, $value){
        if(true === $this->_validatingName($name)){
            $this->_properties->{$name} = $value;
        }

        return $this;
    }

    /**
     * Remove a static property
     * @param string $name
     * @return $this
     */
    public function deleteStaticProperty($name){
        if(true === $this->_validatingName($name) && isset($this->_properties->{$name})){
            unset($this->_properties->{$name});
        }

        return $this;
    }

    /**
     * Return to the closure a static property
     * @param string $name
     * @return mixed
     */
    public function getStaticProperty($name){
        if(true === $this->_validatingName($name) && isset($this->_properties->{$name})){
            return $this->_properties->{$name};
        }

        return null;
    }

    /**
     * Check if a static property is stored
     * @param string $name
     * @return boolean
     */
    public function testStaticProperty($name){
        return true === $this->_validatingName($name) && isset($this->_properties->{$name});
    }
}