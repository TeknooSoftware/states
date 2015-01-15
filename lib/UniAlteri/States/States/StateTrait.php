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
 * @subpackage  States
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://agence.net.ua)
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 */

namespace UniAlteri\States\States;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;

/**
 * Class StateTrait
 * Standard implementation of the state interface for state class.
 * It's a trait to allow developer to implement easily the interface for their state class.
 *
 * @package     States
 * @subpackage  States
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait StateTrait
{
    /**
     * DI Container to use for this object
     * @var DI\ContainerInterface
     */
    protected $diContainer = null;

    /**
     * List of methods available for this state
     * @var \ArrayObject
     */
    protected $methodsListArray = null;

    /**
     * Reflection class object of this state to extract closures and description
     * @var \ReflectionClass
     */
    protected $reflectionClass = null;

    /**
     * Reflections methods of this state to extract description and closures
     * @var \ReflectionMethod[]
     */
    protected $reflectionsMethods = null;

    /**
     * List of closures already extracted and set into Injection Closure Container
     * @var DI\InjectionClosureInterface[]
     */
    protected $closuresObjects = null;

    /**
     * Methods to not return into descriptions
     * @var array
     */
    protected $methodsNamesToIgnoreArray = array(
        '__construct' => '__construct',
        '__destruct' => '__destruct',
        'getReflectionClass' => 'getReflectionClass',
        'buildInjectionClosureObject' => 'buildInjectionClosureObject',
        'checkVisibility' => 'checkVisibility',
        'setDIContainer' => 'setDIContainer',
        'getDIContainer' => 'getDIContainer',
        'listMethods' => 'listMethods',
        'testMethod' => 'testMethod',
        'getMethodDescription' => 'getMethodDescription',
        'getClosure' => 'getClosure',
    );

    /**
     * To build the ReflectionClass for the current object
     * @api
     * @return \ReflectionClass
     */
    protected function getReflectionClass()
    {
        if (null === $this->reflectionClass) {
            $this->reflectionClass = new \ReflectionClass(\get_class($this));
        }

        return $this->reflectionClass;
    }

    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->diContainer;
    }

    /**
     * To return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods()
    {
        if (null === $this->methodsListArray) {
            //Extract methods
            $thisReflectionClass = $this->getReflectionClass();
            $flags = \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE;
            $methodsArray = $thisReflectionClass->getMethods($flags);

            //Extract methods' names
            $methodsFinalArray = new \ArrayObject();
            foreach ($methodsArray as $methodReflection) {
                //We ignore all static methods, there are incompatible with stated behavior
                if (false === $methodReflection->isStatic()) {
                    //Store reflection into the local cache
                    $methodNameString = $methodReflection->getName();
                    if (!isset($this->methodsNamesToIgnoreArray[$methodNameString])) {
                        $methodsFinalArray[] = $methodNameString;
                        $this->reflectionsMethods[$methodNameString] = $methodReflection;
                    }
                }
            }

            $this->methodsListArray = $methodsFinalArray;
        }

        return $this->methodsListArray;
    }

    /**
     * To check if the method is available in the scope
     * @api
     * @param  string                    $methodName
     * @param  string                    $scope
     * @return bool
     * @throws Exception\InvalidArgument
     */
    protected function checkVisibility($methodName, $scope)
    {
        $visible = false;
        if (isset($this->reflectionsMethods[$methodName])) {
            //Check visibility scope
            switch ($scope) {
                case StateInterface::VISIBILITY_PRIVATE:
                    //Private, can access all
                    $visible = true;
                    break;
                case StateInterface::VISIBILITY_PROTECTED:
                    //Can not access to private methods
                    if (false === $this->reflectionsMethods[$methodName]->isPrivate()) {
                        //It's a private method, do like if there is no method
                        $visible = true;
                    }
                    break;
                case StateInterface::VISIBILITY_PUBLIC:
                    //Can not access to protect and private method.
                    if (true === $this->reflectionsMethods[$methodName]->isPublic()) {
                        //It's not a public method, do like if there is no method
                        $visible = true;
                    }
                    break;
                default:
                    //Bad parameter, throws exception
                    throw new Exception\InvalidArgument('Error, the visibility scope is not recognized');
                    break;
            }
        }

        return $visible;
    }

    /**
     * To test if a method exists for this state in the current visibility scope.
     * @param  string                    $methodName
     * @param  string                    $scope      self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @return boolean
     * @throws Exception\InvalidArgument when the method name is not a string
     */
    public function testMethod($methodName, $scope = StateInterface::VISIBILITY_PUBLIC)
    {
        if (!is_string($methodName)) {
            throw new Exception\InvalidArgument('Error, the method name is not a valid string');
        }

        //Method is already extracted
        if (isset($this->reflectionsMethods[$methodName])) {
            if ($this->reflectionsMethods[$methodName] instanceof \ReflectionMethod) {
                return $this->checkVisibility($methodName, $scope);
            } else {
                return false;
            }
        }

        try {
            //Try extract description
            $this->getMethodDescription($methodName);
        } catch (\Exception $e) {
            //Method not found, store locally the result
            $this->reflectionsMethods[$methodName] = false;

            return false;
        }

        //Return the result according with the visibility
        return $this->checkVisibility($methodName, $scope);
    }

    /**
     * To return the description of a method to configure the behavior of the proxy. Return also description of private
     * methods
     * @param  string                         $methodName
     * @return \ReflectionMethod
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws Exception\InvalidArgument      when the method name is not a string
     */
    public function getMethodDescription($methodName)
    {
        if (!is_string($methodName)) {
            throw new Exception\InvalidArgument('Error, the method name is not a valid string');
        }

        if (isset($this->methodsNamesToIgnoreArray[$methodName])) {
            throw new Exception\MethodNotImplemented('Error, this method is not implemented by this state');
        }

        $thisReflectionClass = $this->getReflectionClass();

        //Initialize ArrayObject to store Reflection Methods
        if (!($this->reflectionsMethods instanceof \ArrayObject)) {
            $this->reflectionsMethods = new \ArrayObject();
        }

        try {
            //Load Reflection Method if it is not already done
            if (!isset($this->reflectionsMethods[$methodName])) {
                $methodDescription = $thisReflectionClass->getMethod($methodName);
                if (false !== $methodDescription->isStatic()) {
                    //Method static are not available
                    throw new Exception\MethodNotImplemented(
                        sprintf('Method "%s" is not available for this state', $methodName)
                    );
                }

                $this->reflectionsMethods[$methodName] = $methodDescription;
            }

            return $this->reflectionsMethods[$methodName];
        } catch (\Exception $e) {
            //Method not found
            throw new Exception\MethodNotImplemented(
                sprintf('Method "%s" is not available for this state', $methodName),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * To build a new Injection Closure object from the service defined in the DI Container
     * @api
     * @return DI\InjectionClosureInterface
     * @throws Exception\IllegalService     when there are no DI Container or Injection Closure Container bought
     */
    protected function buildInjectionClosureObject()
    {
        $container = $this->getDIContainer();
        if (!$container instanceof DI\ContainerInterface) {
            throw new Exception\IllegalService('Error, no DI Container has been defined');
        }

        $diContainer = $container->get(StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER);
        if (!$diContainer instanceof DI\InjectionClosureInterface) {
            throw new Exception\IllegalService('Error, no Injection Container has been defined');
        }

        return $diContainer;
    }

    /**
     * To return a closure of the required method to use in the proxy, according with the current visibility scope
     * @param  string                         $methodName
     * @param  Proxy\ProxyInterface           $proxy
     * @param  string                         $scope      self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @return DI\InjectionClosureInterface
     * @throws Exception\MethodNotImplemented is the method does not exist or not available in this scope
     * @throws Exception\InvalidArgument      when the method name is not a string
     * @throws Exception\IllegalProxy         when the proxy does not implement the good interface
     * @throws Exception\IllegalService       when there are no DI Container or Injection Closure Container bought
     */
    public function getClosure($methodName, $proxy, $scope = StateInterface::VISIBILITY_PUBLIC)
    {
        if (!is_string($methodName)) {
            throw new Exception\InvalidArgument('Error, the method name is not a valid string');
        }

        if (!($this->closuresObjects instanceof \ArrayObject)) {
            //Initialize locale closure cache
            $this->closuresObjects = new \ArrayObject();
        }

        if (!$proxy instanceof Proxy\ProxyInterface) {
            throw new Exception\IllegalProxy('Error, the proxy does not implement the required proxy');
        }

        if (!isset($this->closuresObjects[$methodName])) {
            //The closure is not already generated
            //Extract them
            $methodReflection = $this->getMethodDescription($methodName);
            $closure = $methodReflection->getClosure($this);

            //Bind $this with proxy
            $closure = \Closure::bind($closure, $proxy, get_class($proxy));

            //Include the closure into the container
            $injectionClosure = $this->buildInjectionClosureObject()->setClosure($closure);
            $injectionClosure->setDIContainer($this->getDIContainer());
            $this->closuresObjects[$methodName] = $injectionClosure;
        }

        //Check visibility scope
        if (false === $this->checkVisibility($methodName, $scope)) {
            throw new Exception\MethodNotImplemented(
                sprintf('Method "%s" is not available for this state', $methodName)
            );
        }

        return $this->closuresObjects[$methodName];
    }
}
