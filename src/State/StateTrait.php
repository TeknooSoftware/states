<?php

/*
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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\State;

use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class StateTrait
 * Default implementation of the state interface, representing states entities in stated class.
 * A trait implementation has been chosen to allow developer to write theirs owns factory, extendable from any class.
 *
 * Objects implementing this interface must find, bind and execute closure via the method executeClosure() for the
 * required method. (Rebind must use \Closure::call() to rebind static, self and $this or rebindTo()).
 *
 * Objects must follow instruction passed to executeClosure() and manage the visibility of the method and not allow
 * executing a private method from an outside call.
 *
 * Result must be injected to the proxy by using the callback passed to executeClosure(). It's allowed to execute a
 * method without inject the result into the proxy instance to allow developers to call several methods. But you can
 * only inject one result by call. (Several implementations available at a same time is forbidden by the proxy
 * interface).
 *
 * Static method are not managed (a class can not have a state, only it's instance).
 *
 * State's methods are not directly executed. They are a builder to create the closure, they must
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
 * Since 7.0, \Closure created from the Reflection Api can not be bound to an another class (only rebind of $this
 * is permitted), so the feature \Closure::call() was not usable. Since 7.1, rebind $this for this special closure
 * is also forbidden.
 *
 * @api
 *
 * @see StateInterface
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
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
     */
    private ?\ReflectionClass $reflectionClass = null;

    /**
     * Reflections methods of this state to extract description and closures.
     *
     * @var \ReflectionMethod[]
     */
    private array $reflectionsMethods = [];

    /**
     * List of closures already extracted and set into Injection Closure Container.
     *
     * @var \Closure[]
     */
    private array $closuresObjects = [];

    /**
     * To know if the private mode is enable or not for this state (see isPrivateMode()).
     */
    private bool $privateModeStatus = false;

    /**
     * To know the full qualified stated class name of the object owning this state container.
     */
    private string $statedClassName;

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
     *
     * @throws \ReflectionException
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
    private function checkVisibilityPrivate(string &$methodName, string &$statedClassOrigin): bool
    {
        if (
            true === $this->privateModeStatus
            && $statedClassOrigin !== $this->statedClassName
            && true === $this->reflectionsMethods[$methodName]->isPrivate()
        ) {
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
    private function checkVisibilityProtected(string &$methodName, string &$statedClassOrigin): bool
    {
        if (
            false === $this->reflectionsMethods[$methodName]->isPrivate()
            && !empty($statedClassOrigin)
            && ($statedClassOrigin === $this->statedClassName
                || \is_subclass_of($statedClassOrigin, $this->statedClassName))
        ) {
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
    private function checkVisibilityPublic(string &$methodName): bool
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
        string &$methodName,
        string &$requiredScope,
        string &$statedClassOrigin
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
     * @throws \ReflectionException
     */
    private function loadMethodDescription(string &$methodName): bool
    {
        if (isset($this->reflectionsMethods[$methodName])) {
            return $this->reflectionsMethods[$methodName] instanceof \ReflectionMethod;
        }

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
     * @param string $methodName
     *
     * @return mixed (value of the stated method called)
     *
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws \ReflectionException
     */
    private function getClosure(
        string &$methodName
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
     * @throws \ReflectionException
     */
    public function executeClosure(
        ProxyInterface $object,
        string &$methodName,
        array &$arguments,
        string &$requiredScope,
        string &$statedClassOrigin,
        callable &$returnCallback
    ): StateInterface {
        $closure = $this->getClosure($methodName);

        //Check visibility scope
        if (
            !$closure instanceof \Closure
            || false === $this->checkVisibility($methodName, $requiredScope, $statedClassOrigin)
        ) {
            return $this;
        }

        if (true === $this->privateModeStatus) {
            $closure = $closure->bindTo($object, $this->statedClassName);
            $returnValue = $closure(...$arguments);
        } else {
            $returnValue = $closure->call($object, ...$arguments);
        }

        $returnCallback($returnValue);

        return $this;
    }
}
