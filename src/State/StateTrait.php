<?php

declare(strict_types=1);

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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

/**
 * @category    States
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\State;
use Teknoo\States\Proxy\ProxyInterface;

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
 *
 * @see StateInterface
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
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
        'getReflectionClass' => 'getReflectionClass',
        'checkVisibilityPrivate' => 'checkVisibilityPrivate',
        'checkVisibilityProtected' => 'checkVisibilityProtected',
        'checkVisibilityPublic' => 'checkVisibilityPublic',
        'checkVisibility' => 'checkVisibility',
        'loadMethodDescription' => 'loadMethodDescription',
        'getClosure' => 'getClosure',
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
        $this->privateModeStatus = $privateMode;
        $this->statedClassName = $statedClassName;
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
     * To check if the caller method can be accessible by the method caller :
     * The called method is protected or public (skip to next test)
     * The private mode is disable for this state (state is not defined is a parent class)
     * The caller method is in the same stated class that the called method.
     *
     * @param string $methodName
     * @param string $statedClassOrigin
     *
     * @return bool
     */
    private function checkVisibilityPrivate(string $methodName, string $statedClassOrigin)
    {
        if (true === $this->privateModeStatus
            && $statedClassOrigin !== $this->statedClassName
            && true === $this->reflectionsMethods[$methodName]->isPrivate()) {
            return false;
        }

        return true;
    }

    /**
     * Can not access to private methods, only public and protected.
     *
     * @param string $methodName
     * @param string $statedClassOrigin
     *
     * @return bool
     */
    private function checkVisibilityProtected(string $methodName, string $statedClassOrigin)
    {
        if (false === $this->reflectionsMethods[$methodName]->isPrivate()
            && !empty($statedClassOrigin)
            && ($statedClassOrigin === $this->statedClassName
                || \is_subclass_of($statedClassOrigin, $this->statedClassName))) {
            //It's a public or protected method, do like if there is no method
            return true;
        }

        return false;
    }

    /**
     * Can not access to protect and private method.
     *
     * @param string $methodName
     *
     * @return bool
     */
    private function checkVisibilityPublic(string $methodName)
    {
        if (true === $this->reflectionsMethods[$methodName]->isPublic()) {
            //It's a public method, do like if there is no method
            return true;
        }

        return false;
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
     * @param string|null $statedClassOrigin
     *
     * @return bool
     *
     * @throws Exception\InvalidArgument
     */
    private function checkVisibility(
        string $methodName,
        string $requiredScope,
        string $statedClassOrigin
    ): bool {
        //Check visibility scope
        switch ($requiredScope) {
            case StateInterface::VISIBILITY_PRIVATE:
                return $this->checkVisibilityPrivate($methodName, $statedClassOrigin);
                break;
            case StateInterface::VISIBILITY_PROTECTED:
                //Can not access to private methods, only public and protected
                return $this->checkVisibilityProtected($methodName, $statedClassOrigin);
                break;
            case StateInterface::VISIBILITY_PUBLIC:
                //Can not access to protect and private method.
                return $this->checkVisibilityPublic($methodName);
                break;
            default:
                //Bad parameter, throws exception
                throw new Exception\InvalidArgument('Error, the visibility scope is not recognized');
                break;
        }
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
     * @return bool
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    private function loadMethodDescription(string $methodName): bool
    {
        if (isset($this->reflectionsMethods[$methodName])) {
            return $this->reflectionsMethods[$methodName] instanceof \ReflectionMethod;
        }

        try {
            $thisReflectionClass = $this->getReflectionClass();
            if (!$thisReflectionClass->hasMethod($methodName)) {
                $this->reflectionsMethods[$methodName] = false;

                return false;
            }

            //Load Reflection Method if it is not already done
            $methodDescription = $thisReflectionClass->getMethod($methodName);
            if (false !== $methodDescription->isStatic()) {
                $this->reflectionsMethods[$methodName] = false;

                return false;
            }

            $this->reflectionsMethods[$methodName] = $methodDescription;

            return true;
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
     * To return a closure of the required method to use in the proxy, in the required scope (check from the visibility
     * of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state or
     *      another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or another
     *      state) and not for its children.
     *
     * @param string      $methodName
     *
     * @return mixed (value of the stated method called)
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    private function getClosure(
        string $methodName
    ): ?\Closure {
        if (isset($this->closuresObjects[$methodName])) {
            return $this->closuresObjects[$methodName];
        }

        //Check if the method exist and prepare description for checkVisibility methods
        if (!$this->loadMethodDescription($methodName)) {
            return null;
        }

        //Call directly the closure builder, more efficient
        $closure = $this->{$methodName}();

        if (!$closure instanceof \Closure) {
            throw new Exception\MethodNotImplemented(
                \sprintf('Method "%s" is not a valid Closure', $methodName)
            );
        }

        $this->closuresObjects[$methodName] = $closure;

        return $this->closuresObjects[$methodName];
    }

    /**
     * {@inheritdoc}
     */
    public function executeClosure(
        ProxyInterface $object,
        string $methodName,
        array $arguments,
        string $requiredScope,
        string $statedClassOrigin,
        callable $returnCallback
    ): StateInterface {
       $closure = $this->getClosure($methodName);

       //Check visibility scope
       if (!$closure instanceof \Closure
           || false === $this->checkVisibility($methodName, $requiredScope, $statedClassOrigin)) {
           return $this;
       }

       $returnValue = $closure->call($object, ...$arguments);
       $returnCallback($returnValue);

       return $this;
    }
}
