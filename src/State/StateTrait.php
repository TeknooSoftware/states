<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

/**
 * @category    States
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\States\State;

/**
 * Class StateTrait
 * Default implementation of the state interface, representing states entities in stated class.
 * A trait implementation has been chosen to allow developer to write theirs owns factory, extendable from any class.
 *
 * Objects implementing this interface must
 * return a usable closure via the method getClosure() for the required method. This method must able to be rebinded
 * by the Closure api (The proxy use \Closure::call() to rebind self and $this). These objects must also provide a
 * \ReflectionMethod instance for theirs state's methods and check also if the proxy instance can access to a private
 * or protected method.
 *
 * State's methods are not directly used by the proxy instance. They are a builder to create the closure, they must
 * return them self the closure. So, writing state differs from previous version, example :
 *
 *      <method visibility> function <method name>(): \Closure
 *      {
 *          return function($arg1, $arg2) {
 *              //your code
 *          };
 *      }
 *      method visibility : public/protected/private, visibility used in the proxy instance, for your method
 *      method name: a string, used in the proxy, for your method.
 *
 * Contrary to previous versions of this library, methods of states's object are not directly converted into a \Closure.
 * Since 7.0, \Closure created from the Reflection Api can not be rebinded to an another class (only rebind of $this
 * is permitted), so the feature \Closure::call() was not usable. Since 7.1, rebind $this for this special closure
 * is also forbidden.
 *
 * @api
 * @see StateInterface
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin StateInterface
 */
trait StateTrait
{
    /**
     * List of methods available for this state.
     *
     * @var string[]
     */
    private $methodsListArray = null;

    /**
     * Reflection class object of this state to extract closures and description.
     *
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * Reflections methods of this state to extract description and closures.
     *
     * @var \ReflectionMethod[]
     */
    private $reflectionsMethods = [];

    /**
     * List of closures already extracted and set into Injection Closure Container.
     *
     * @var \Closure[]
     */
    private $closuresObjects = [];

    /**
     * Methods to not return into descriptions.
     *
     * @var array
     */
    protected $methodsNamesToIgnoreArray = array(
        '__construct' => '__construct',
        '__destruct' => '__destruct',
        'getReflectionClass' => 'getReflectionClass',
        'checkVisibility' => 'checkVisibility',
        'listMethods' => 'listMethods',
        'testMethod' => 'testMethod',
        'getMethodDescription' => 'getMethodDescription',
        'getClosure' => 'getClosure',
        'setPrivateMode' => 'setPrivateMode',
        'isPrivateMode' => 'isPrivateMode',
        'getStatedClassName' => 'getStatedClassName',
        'setStatedClassName' => 'setStatedClassName',
    );

    /**
     * To know if the private mode is enable or not for this state (see isPrivateMode()).
     *
     * @var bool
     */
    private $privateModeStatus = false;

    /**
     * To know the full qualified stated class name of the object owning this state container.
     *
     * @var string
     */
    private $statedClassName;

    /**
     * {@inheritdoc}
     */
    public function __construct(bool $privateMode, string $statedClassName)
    {
        $this->setPrivateMode($privateMode);
        $this->setStatedClassName($statedClassName);
    }

    /**
     * To build the ReflectionClass for the current object.
     *
     * @api
     *
     * @return \ReflectionClass
     */
    private function getReflectionClass(): \ReflectionClass
    {
        if (null === $this->reflectionClass) {
            $this->reflectionClass = new \ReflectionClass(\get_class($this));
        }

        return $this->reflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): string
    {
        return $this->statedClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatedClassName(string $statedClassName): StateInterface
    {
        $this->statedClassName = $statedClassName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrivateMode(): bool
    {
        return $this->privateModeStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrivateMode(bool $enable): StateInterface
    {
        $this->privateModeStatus = !empty($enable);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function listMethods(): array
    {
        if (null === $this->methodsListArray) {
            //Extract methods available in this states (all methods, public, protected and private)
            $thisReflectionClass = $this->getReflectionClass();
            $flags = \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE;
            $methodsArray = $thisReflectionClass->getMethods($flags);

            //Extract methods' names
            $methodsFinalArray = [];
            foreach ($methodsArray as $methodReflection) {
                //We ignore all static methods, there are incompatible with stated behavior :
                //State can be only applied on instances entities like object,
                // and not on static entities which by nature have no states
                if (false === $methodReflection->isStatic()
                    && (false === $this->privateModeStatus || false === $methodReflection->isPrivate())) {
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
     * To check if the method is available in the required scope (check from the visibility of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state
     *      or another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or
     *      another state) and not for its children.
     *
     * @param string      $methodName
     * @param string      $requiredScope
     * @param string|null $statedClassOriginName
     *
     * @return bool
     *
     * @throws Exception\InvalidArgument
     */
    private function checkVisibility(
        string $methodName,
        string $requiredScope,
        string $statedClassOriginName
    ): bool {
        $visible = false;
        if (isset($this->reflectionsMethods[$methodName])) {
            //Check visibility scope
            switch ($requiredScope) {
                case StateInterface::VISIBILITY_PRIVATE:
                    //To check if the caller method can be accessible by the method caller :
                    //The called method is protected or public (skip to next test)
                    //The private mode is disable for this state (state is not defined is a parent class)
                    //The caller method is in the same stated class that the called method
                    $privateMethodIsAvailable = true;
                    if (true === $this->privateModeStatus) {
                        if ($statedClassOriginName !== $this->statedClassName) {
                            if (true === $this->reflectionsMethods[$methodName]->isPrivate()) {
                                $privateMethodIsAvailable = false;
                            }
                        }
                    }

                    $visible = $privateMethodIsAvailable;
                    break;
                case StateInterface::VISIBILITY_PROTECTED:
                    //Can not access to private methods, only public and protected
                    if (false === $this->reflectionsMethods[$methodName]->isPrivate()
                        && !empty($statedClassOriginName)
                        && ($statedClassOriginName === $this->statedClassName
                            || \is_subclass_of($statedClassOriginName, $this->statedClassName))) {
                        //It's a public or protected method, do like if there is no method
                        $visible = true;
                    }
                    break;
                case StateInterface::VISIBILITY_PUBLIC:
                    //Can not access to protect and private method.
                    if (true === $this->reflectionsMethods[$methodName]->isPublic()) {
                        //It's a public method, do like if there is no method
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
     * {@inheritdoc}
     */
    public function testMethod(
        string $methodName,
        string $requiredScope,
        string $statedClassOriginName
    ): bool {
        //Method is already extracted
        if (isset($this->reflectionsMethods[$methodName])) {
            if ($this->reflectionsMethods[$methodName] instanceof \ReflectionMethod) {
                return $this->checkVisibility($methodName, $requiredScope, $statedClassOriginName);
            } else {
                return false;
            }
        }

        //Method not already extraced, perform it before check its visibility.
        try {
            //Try extract description
            $this->getMethodDescription($methodName);
        } catch (\Throwable $e) {
            //Method not found, store locally the result
            $this->reflectionsMethods[$methodName] = false;

            return false;
        }

        //Return the result according with the visibility
        return $this->checkVisibility($methodName, $requiredScope, $statedClassOriginName);
    }

    /**
     * To return the description of a method to configure the behavior of the proxy. Return also description of private
     * methods : getMethodDescription() does not check if the caller is allowed to call the required method.
     *
     * getMethodDescription() ignores static method, because there are incompatible with the stated behavior :
     * State can be only applied on instances entities like object,
     * and not on static entities which by nature have no states
     *
     * @api
     *
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    private function getMethodDescription(string $methodName): \ReflectionMethod
    {
        $thisReflectionClass = $this->getReflectionClass();

        try {
            //Load Reflection Method if it is not already done
            if (!isset($this->reflectionsMethods[$methodName])) {
                $methodDescription = $thisReflectionClass->getMethod($methodName);
                if (false !== $methodDescription->isStatic()) {
                    //Ignores static method, because there are incompatible with the stated behavior :
                    // State can be only applied on instances entities like object,
                    // and not on static entities which by nature have no states
                    throw new Exception\MethodNotImplemented(
                        \sprintf('Method "%s" is not available for this state', $methodName)
                    );
                }

                $this->reflectionsMethods[$methodName] = $methodDescription;
            }

            return $this->reflectionsMethods[$methodName];
        } catch (\Throwable $e) {
            //Method not found
            throw new Exception\MethodNotImplemented(
                \sprintf('Method "%s" is not available for this state', $methodName),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClosure(
        string $methodName,
        string $requiredScope,
        string $statedClassOriginName
    ): \Closure {
        if (!isset($this->closuresObjects[$methodName])) {
            //Check if the method exist and prepare description for checkVisibility methods
            $this->getMethodDescription($methodName);

            //Call directly the closure builder, more efficient
            $closure = $this->{$methodName}();

            if (!$closure instanceof \Closure) {
                throw new Exception\MethodNotImplemented(
                    \sprintf('Method "%s" is not a valid Closure', $methodName)
                );
            }

            $this->closuresObjects[$methodName] = $closure;
        }

        //Check visibility scope
        if (false === $this->checkVisibility($methodName, $requiredScope, $statedClassOriginName)) {
            throw new Exception\MethodNotImplemented(
                \sprintf('Method "%s" is not available for this state', $methodName)
            );
        }

        return $this->closuresObjects[$methodName];
    }
}
