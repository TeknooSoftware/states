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
 * @subpackage  States
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\States;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;

/**
 * Class TraitState
 * @package UniAlteri\States\States
 * Standard implementation of the state interface for state class.
 *
 * It's tray to allow developer to implement easily the interface for theirs state class.
 */
trait TraitState
{
    /**
     * DI Container to use for this object
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * List of methods available for this state
     * @var \ArrayObject
     */
    protected $_methodsListArray = null;

    /**
     * Reflection class object of this state to extract closures and description
     * @var \ReflectionClass
     */
    protected $_reflectionClass = null;

    /**
     * Reflections methods of this state to extract description and closures
     * @var \ReflectionMethod[]
     */
    protected $_reflectionsMethods = null;

    /**
     * List of closure already extracted and set into Injection Closure Container
     * @var DI\InjectionClosureInterface[]
     */
    protected $_closuresObjects = null;

    /**
     * Build the ReflectionClass for the current object
     * @return \ReflectionClass
     */
    protected function _getReflectionClass()
    {
        if (null === $this->_reflectionClass) {
            $this->_reflectionClass = new \ReflectionClass(\get_class($this));
        }

        return $this->_reflectionClass;
    }

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * Return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods()
    {
        if (null === $this->_methodsListArray) {
            //Extract methods
            $thisReflectionClass = $this->_getReflectionClass();
            $flags = \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE;
            $methodsArray = $thisReflectionClass->getMethods($flags);

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
            foreach ($methodsArray as $methodReflection) {
                //Store reflection into local cache
                $methodNameString = $methodReflection->getName();
                if (!isset($methodsNamesToIgnoreArray[$methodNameString])) {
                    $methodsFinalArray[] = $methodNameString;
                    $this->_reflectionsMethods[$methodNameString] = $methodReflection;
                }
            }

            $this->_methodsListArray = $methodsFinalArray;
        }

        return $this->_methodsListArray;
    }

    /**
     * Test if a method exist for this state
     * @param string $methodName
     * @return boolean
     */
    public function testMethod($methodName)
    {
        //Method is already extracted
        if (isset($this->_reflectionsMethods[$methodName])) {
            if ($this->_reflectionsMethods[$methodName] instanceof \ReflectionMethod) {
                return true;
            } else {
                return false;
            }
        }

        try {
            //Try extract description
            $this->getMethodDescription($methodName);
            return true;
        } catch(\Exception $e) {
            //Method not found, store localy the result
            $this->_reflectionsMethods[$methodName] = false;
            return false;
        }
    }

    /**
     * Return the description of a method to configure the behavior of the proxy
     * @param string $methodName
     * @return \ReflectionMethod
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getMethodDescription($methodName)
    {
        $thisReflectionClass = $this->_getReflectionClass();

        //Initialize ArrayObject to store Reflection Methods
        if (!($this->_reflectionsMethods instanceof \ArrayObject)) {
            $this->_reflectionsMethods = new \ArrayObject();
        }

        try {
            //Load Reflection Methods if it is not already done
            if (!isset($this->_reflectionsMethods[$methodName])) {
                $this->_reflectionsMethods[$methodName] = $thisReflectionClass->getMethod($methodName);
            }

            return $this->_reflectionsMethods[$methodName];
        } catch(\Exception $e) {
            //Method not found
            throw new Exception\MethodNotImplemented(
                'Method "'.$methodName.'" is not available for this state',
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build a new Injection Closure object
     * @return DI\InjectionClosureInterface
     */
    protected function _buildInjectionClosureObject()
    {
        return $this->getDIContainer()->get(StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
    }

    /**
     * Return a closure of the required method to use in the proxy
     * @param string $methodName
     * @param Proxy\ProxyInterface $proxy
     * @return DI\InjectionClosureInterface
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getClosure($methodName, Proxy\ProxyInterface $proxy)
    {
        if (!($this->_closuresObjects instanceof \ArrayObject)) {
            //Initialize locale closure cache
            $this->_closuresObjects = new \ArrayObject();
        }

        if (!isset($this->_closuresObjects[$methodName])) {
            //The closure is not already generated
            //Extract them
            $methodReflection = $this->getMethodDescription($methodName);
            $closure = $methodReflection->getClosure($this);

            //Bind $this with proxy
            $closure = \Closure::bind($closure, $proxy, get_class($proxy));

            //Include the closure into the container
            $args = array($closure);
            $injectionClosure = $this->_buildInjectionClosureObject()->setClosure($args);
            $injectionClosure->setDIContainer($this->getDIContainer());
            $this->_closuresObjects[$methodName] = $injectionClosure;
        }

        return $this->_closuresObjects[$methodName];
    }
}