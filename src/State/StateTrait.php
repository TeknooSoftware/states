<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\State;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use SensitiveParameter;
use Teknoo\States\Proxy\ProxyInterface;

use function get_debug_type;
use function is_subclass_of;

/**
 * Default implementation of the state interface, representing states entities in stated class.
 * A trait implementation has been chosen to allow developer to write theirs owns factory, extendable from any class.
 *
 * Objects implementing this interface must find, bind and execute closure via the method executeClosure() for the
 * required method. (Rebind must use `\Closure::call()` to rebind `static`, `self` and `$this` or `\Closure::bindTo()`).
 *
 * Objects must follow instruction passed to `executeClosure()` and manage the visibility of the method and not allow
 * executing a private method from an outside call.
 *
 * Result must be injected to the proxy by using the callback passed to `executeClosure()`. It's allowed to execute a
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
 * Since 7.0, `\Closure` created from the Reflection Api can not be bound to an another class (only rebind of $this
 * is permitted), so the feature `\Closure::call()` was not usable. Since 7.1, rebind $this for this special closure
 * is also forbidden.
 *
 * @api
 *
 * @see StateInterface
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin StateInterface
 */
trait StateTrait
{
    /**
     * Reflections of this state and of its methods, closures returned by its builders and results of visibility's
     * checks, kept to not compute them at each call. They are stored in a dedicated object, always serialized as an
     * empty object, because reflections and closures are not serializable : a state, and so its stated class
     * instances, must stay serializable after a call. It is created on demand.
     */
    private ?RuntimeCache $runtimeCache = null;

    /**
     * @param class-string $statedClassName
     */
    public function __construct(
        private bool $privateModeStatus,
        private string $statedClassName,
    ) {
    }

    /**
     * To build the ReflectionClass for the current object.
     *
     * @api
     *
     * @return ReflectionClass<covariant object>
     * @throws ReflectionException
     */
    private function getReflectionClass(): ReflectionClass
    {
        $cache = $this->runtimeCache ??= new RuntimeCache();

        return $cache->reflectionClass ??= new ReflectionClass($this::class);
    }

    /**
     * To check if the caller method can be accessible by the method caller :
     * The called method is protected or public (skip to next test)
     * The private mode is disable for this state (state is not defined is a parent class)
     * The caller method is in the same stated class that the called method.
     */
    private function checkVisibilityPrivate(string &$methodName, string &$statedClassOrigin): bool
    {
        $methodDescription = $this->runtimeCache?->reflectionsMethods[$methodName] ?? false;

        if (
            true === $this->privateModeStatus
            && $statedClassOrigin !== $this->statedClassName
            && $methodDescription instanceof ReflectionMethod
            && true === $methodDescription->isPrivate()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Can not access to private methods, only public and protected.
     */
    private function checkVisibilityProtected(string &$methodName, string &$statedClassOrigin): bool
    {
        $methodDescription = $this->runtimeCache?->reflectionsMethods[$methodName] ?? false;

        //It's a public or protected method, do like if there is no method
        return $methodDescription instanceof ReflectionMethod
            && false === $methodDescription->isPrivate()
            && !empty($statedClassOrigin)
            && (
                $statedClassOrigin === $this->statedClassName
                || is_subclass_of($statedClassOrigin, $this->statedClassName)
            );
    }

    /**
     * Can not access to protect and private method.
     */
    private function checkVisibilityPublic(string &$methodName): bool
    {
        $methodDescription = $this->runtimeCache?->reflectionsMethods[$methodName] ?? false;

        //It's a public method, do like if there is no method
        return $methodDescription instanceof ReflectionMethod
            && true === $methodDescription->isPublic();
    }

    /**
     * To check if the method is available in the required scope (check from the visibility of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state
     *      or another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or
     *      another state) and not for its children.
     *
     * @throws Exception\InvalidArgument
     */
    private function checkVisibility(
        string &$methodName,
        Visibility $scope,
        string &$statedClassOrigin
    ): bool {
        $cache = $this->runtimeCache ??= new RuntimeCache();

        //Check visibility scope
        return $cache->visibilityCache[$scope->value][$statedClassOrigin][$methodName] ??= match ($scope) {
            Visibility::Private => $this->checkVisibilityPrivate($methodName, $statedClassOrigin),
            Visibility::Protected => $this->checkVisibilityProtected($methodName, $statedClassOrigin),
            Visibility::Public => $this->checkVisibilityPublic($methodName),
        };
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
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws ReflectionException
     */
    private function loadMethodDescription(string &$methodName): bool
    {
        $cache = $this->runtimeCache ??= new RuntimeCache();

        if (isset($cache->reflectionsMethods[$methodName])) {
            return $cache->reflectionsMethods[$methodName] instanceof ReflectionMethod;
        }

        $thisReflectionClass = $this->getReflectionClass();
        if (!$thisReflectionClass->hasMethod($methodName)) {
            $cache->reflectionsMethods[$methodName] = false;

            return false;
        }

        //Load Reflection Method if it is not already done
        $methodDescription = $thisReflectionClass->getMethod($methodName);
        if (
            false !== $methodDescription->isStatic()
            || $methodDescription->isConstructor()
            || $methodDescription->getNumberOfRequiredParameters() > 0
            || __FILE__ === $methodDescription->getFileName()
        ) {
            //Static methods are not managed, and methods of the state's implementation (constructor, methods of
            //this trait, methods with required arguments, a builder is always called without argument) are not
            //methods of the stated class : they must not be callable from the proxy
            $cache->reflectionsMethods[$methodName] = false;

            return false;
        }

        $cache->reflectionsMethods[$methodName] = $methodDescription;

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
     * @throws Exception\MethodNotImplemented is the method does not exist
     * @throws ReflectionException
     */
    private function getClosure(
        string &$methodName
    ): Closure {
        $cache = $this->runtimeCache ??= new RuntimeCache();

        if (isset($cache->closuresObjects[$methodName])) {
            return $cache->closuresObjects[$methodName];
        }

        //The method's description is always loaded and checked by executeClosure() before building the closure.
        //Call the closure builder from its reflection : this trait can be used by a parent class of the state
        //(like AbstractState), and a private builder is not callable from the scope of this parent class.
        //It is performed only once by builder, the closure is kept to be reused.
        $methodDescription = $cache->reflectionsMethods[$methodName] ?? null;
        $closure = null;
        if ($methodDescription instanceof ReflectionMethod) {
            $closure = $methodDescription->invoke($this);
        }

        if (!$closure instanceof Closure) {
            throw new Exception\MethodNotImplemented(
                "Method '$methodName' is not a valid Closure : the builder " . $this::class . "::$methodName() "
                    . 'must return a Closure, ' . get_debug_type($closure) . ' returned'
            );
        }

        //A static closure is supported, but it can not be bound to the stated class instance, only to its scope
        $cache->staticClosures[$methodName] = new ReflectionFunction($closure)->isStatic();
        $cache->closuresObjects[$methodName] = $closure;

        return $closure;
    }

    /**
     * @throws ReflectionException
     */
    public function executeClosure(
        ProxyInterface $object,
        string &$methodName,
        #[SensitiveParameter] array &$arguments,
        Visibility $requiredScope,
        string &$statedClassOrigin,
        callable &$returnCallback
    ): StateInterface {
        $cache = $this->runtimeCache ??= new RuntimeCache();

        //Fast path, without any other method call : the closure is already built and the visibility was already
        //checked for this scope and this caller. The result read here is the one computed by checkVisibility().
        $closure = $cache->closuresObjects[$methodName] ?? null;
        $isAllowed = $cache->visibilityCache[$requiredScope->value][$statedClassOrigin][$methodName] ?? null;

        if (null === $closure || null === $isAllowed) {
            //Check visibility scope, before building the closure : a builder must not be executed for a denied caller
            if (
                !$this->loadMethodDescription($methodName)
                || false === $this->checkVisibility($methodName, $requiredScope, $statedClassOrigin)
            ) {
                return $this;
            }

            $closure = $this->getClosure($methodName);
        } elseif (false === $isAllowed) {
            return $this;
        }

        $isStatic = $cache->staticClosures[$methodName] ?? false;

        if (true === $this->privateModeStatus || true === $isStatic) {
            //The scope is the class of the proxy, like with \Closure::call(), except with the private mode : it is
            //the stated class owning this state.
            $scope = $object::class;
            if (true === $this->privateModeStatus) {
                $scope = $this->statedClassName;
            }

            //A static closure does not use $this : PHP forbids to bind an instance to it ("Cannot bind an instance to
            //a static closure", an error since PHP 9). Only its scope (self and static) is bound to the stated class.
            $newThis = $object;
            if (true === $isStatic) {
                $newThis = null;
            }

            $closure = Closure::bind($closure, $newThis, $scope);
            if ($closure instanceof Closure) {
                $returnValue = $closure(...$arguments);
                $returnCallback($returnValue);
            }
        } else {
            $returnValue = $closure->call($object, ...$arguments);
            $returnCallback($returnValue);
        }

        return $this;
    }
}
