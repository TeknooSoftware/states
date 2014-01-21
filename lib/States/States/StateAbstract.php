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

namespace UniAlteri\States\States;

abstract class StateAbstract implements StateInterface{

    /**
     * @var \UniAlteri\States\DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * @var \ArrayObject
     */
    protected $_methodsListArray = null;

    /**
     *
     * @var \ReflectionClass
     */
    protected $_reflectionClass = null;

    /**
     *
     * @var \ArrayObject
     */
    protected $_reflectionsMethods = null;

    /**
     * @var \ArrayObject
     */
    protected $_closuresObjects = null;

    /**
     * Build the ReflectionClass for the current object
     * @return \ReflectionClass
     */
    protected function _getReflectionClass(){
        if(null === $this->_reflectionClass){
            $this->_reflectionClass = new \ReflectionClass(\get_class($this));
        }

        return $this->_reflectionClass;
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
     * Return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods(){
        if(null === $this->_methodsListArray){
            $thisReflectionClass = $this->_getReflectionClass();
            $methodsArray = $thisReflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE | \ReflectionMethod::IS_FINAL);

            //Methods to not return into descriptions
            $methodsNamesToIgnoreArray = array_flip(
                array(
                    'setDIContainer',
                    'getDIContainer',
                    'listMethods',
                    'getMethodDescription',
                    'getClosure'
                )
            );

            //Extracts methods' names
            $methodsFinalArray = new \ArrayObject();
            foreach($methodsArray as $methodReflection){
                $methodNameString = $methodReflection->getName();
                if(!isset($methodsNamesToIgnoreArray[$methodNameString])){
                    $methodsFinalArray[] = $methodNameString;
                    $this->_reflectionsMethods[$methodNameString] = $methodReflection;
                }
            }

            $this->_methodsListArray = $methodsFinalArray;
        }

        return $this->_methodsListArray;
    }

    /**
     * Test if a method exist into the
     * @param string $methodName
     * @return boolean
     */
    public function testMethod($methodName){
        if(isset($this->_reflectionsMethods[$methodName])){
            if($this->_reflectionsMethods[$methodName] instanceof \ReflectionMethod){
                return true;
            }
            else{
                return false;
            }
        }

        try{
            $this->getMethodDescription($methodName);
            return true;
        }
        catch(\Exception $e){
            $this->_reflectionsMethods[$methodName] = false;
            return false;
        }
    }

    /**
     * Return the description of a method to configure the behavior of the proxy
     * @param string $methodName
     * @return \ReflectionMethod
     */
    public function getMethodDescription($methodName){
        $thisReflectionClass = $this->_getReflectionClass();

        //Initialize ArrayObject to store Reflection Methods
        if(!($this->_reflectionsMethods instanceof \ArrayObject)){
            $this->_reflectionsMethods = new \ArrayObject();
        }

        try{
            //Load Reflection Methods if it is not already done
            if(!isset($this->_reflectionsMethods[$methodName])){
                $this->_reflectionsMethods[$methodName] = $thisReflectionClass->getMethod($methodName);
            }

            return $this->_reflectionsMethods[$methodName];
        }
        catch(\Exception $e){
            throw new \UniAlteri\States\Exception\MethodNotFound(
                'Method "'.$methodName.'" is not available for this state',
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build a new Injection Closure object
     * @return \UniAlteri\States\DI\InjectionClosureInterface
     */
    protected function _buildInjectionClosureObject(){
        return $this->getDIContainer()->get(StateInterface::INJECTION_CLOSURE_IDENTIFIER);
    }

    /**
     * Return a closure of the required method to use in the proxy
     * @param string $methodName
     * @param \UniAlteri\States\Proxy\ProxyInterface $proxy
     * @return \UniAlteri\States\DI\InjectionClosureInterface
     */
    public function getClosure($methodName, \UniAlteri\States\Proxy\ProxyInterface $proxy){
        if(!($this->_closuresObjects instanceof \ArrayObject)){
            $this->_closuresObjects = new \ArrayObject();
        }

        if(!isset($this->_closuresObjects[$methodName])){
            $methodReflection = $this->getMethodDescription($methodName);
            $closure = $methodReflection->getClosure($this);

            $closure = \Closure::bind($closure, $proxy, get_class($proxy));

            $args = array($closure);
            $injectionClosure = $this->_buildInjectionClosureObject()->setClosure($args);
            $injectionClosure->setDIContainer($this->getDIContainer());
            $this->_closuresObjects[$methodName] = $injectionClosure;
        }

        return $this->_closuresObjects[$methodName];
    }
}