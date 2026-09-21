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

namespace Teknoo\States\Proxy;

use ReflectionClass;
use ReflectionAttribute;
use ReflectionMethod;
use SensitiveParameter;
use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Exception\WrongConfiguration;
use Teknoo\States\Proxy\Exception\StateNotFound;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\Visibility;

use function array_flip;
use function array_key_last;
use function array_merge;
use function array_keys;
use function array_pop;
use function array_unique;
use function class_exists;
use function count;
use function debug_backtrace;
use function get_parent_class;
use function interface_exists;
use function is_a;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function is_subclass_of;
use function ltrim;
use function sort;
use function sprintf;
use function strrpos;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Implementation of the proxy class in stated class. It is used in this library to create stated class instance.
 *
 * The proxy, by default, redirect all calls, of non defined methods in the proxy, to enabled states.
 * $this, static and self keywords in all methods the stated class instance (aka in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory.states configurations must be defined with the class attribute
 * #[Teknoo\States\Attributes\StateClass()]
 *
 * States can be overload by children of a stated class : The overloading uses only the non qualified name.
 *
 * Since 3.0, states's methods are a builder, returning a real closure to use. The state must not use
 * the Reflection API to extract the closure (`Closure` from `Reflection` are not bindable on a new scope since 7.1).
 * States can be also an anonymous class, it's name must be defined by an interface, implementing by this state.
 *
 * Since 3.2, the library following #east programming rules, all methods designed to know the state of the object are
 * been removed. Closures are now bound (with the proxy) and called by states's managing object and not directly by
 * the proxy. Result are then injected into proxy. This behavior allows developers to call several methods before return
 * the result. (But only one result is granted).
 *
 * @see ProxyInterface
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin ProxyInterface
 */
trait ProxyTrait
{
    /**
     * List of currently enabled states in this proxy.
     *
     * @var array<string, StateInterface>
     */
    private array $activesStates = [];

    /**
     * List of available states for this stated class instance.
     *
     * @var array<string, StateInterface>
     */
    private array $states = [];

    /**
     * To register for each state, the proxy class owning it.
     *
     * @var array<string, string>
     */
    private array $classesByStates = [];

    /**
     * To keep the list of full qualified state in parent classes to allow enable overload/redefined state with
     * original full qualified state name.
     *
     * @var array<string, string>
     */
    private array $statesAliasesList = [];

    /**
     * Stack to know the caller full qualified stated class when an internal method call a parent method to forbid
     * private method access : stated classes owning states' methods in execution, the last one is the current caller.
     * It is an array, directly managed by the method `__call()`, to avoid several methods' calls at each call.
     *
     * @var list<string>
     */
    private array $callersStack = [];

    /**
     * Cache to store the name of the selected state for a method to avoid search at each call of the same method
     * This cache is cleared at each change of active state
     *
     * @var array<string, array<string, string>>
     */
    private array $calledMethodCache = [];

    /**
     * Default class name extracted from call stack by extractVisibilityScopeFromObject
     */
    private string $defaultCallerStatedClassName = '';

    /**
     * @var array<string, array{
     *     0: array<string, StateInterface>,
     *     1: array<string, StateInterface>,
     *     2: array<string, string>,
     *     3: array<string, string>
     * }>
     */
    private static array $loadedStatesCaches = [];

    /**
     * Deprecated
     * List all states's classes available in this state. It's not mandatory to redefine states of parent's class,
     * They are automatically loaded by the proxy. Warning, if you redeclare a state of a parent's class with its full
     * qualified class name, you can access to its private method: this declaration overloads the parent's state and
     * the state is owned by the child class.
     *
     * Example:
     * return [
     *  myFirstState::class,
     *  mySecondState::class
     * ];
     *
     * @internal
     * @return array<string>
     * abstract protected static function statesListDeclaration(): array;
    */

    /**
     * To instantiate a state class defined in this proxy. Is a state have a same non fullqualified class name of
     * a previous loaded state (defined in previously in this class or in children) it's skipped.
     *
     * @param array<string> $statesList
     * @param array<string> $loadedStatesList
     *
     * @throws Exception\StateNotFound
     */
    private function initializeStates(
        array $statesList,
        bool $enablePrivateMode,
        string &$selfClassName,
        array &$loadedStatesList
    ): void {
        foreach ($statesList as $stateClassName) {
            //Extract non qualified class name and check if this state is not already loaded
            $shortStateName = ltrim(substr($stateClassName, (int) strrpos($stateClassName, '\\')), '\\');
            if (isset($loadedStatesList[$shortStateName])) {
                $this->statesAliasesList[$stateClassName] = $loadedStatesList[$shortStateName];
                $this->classesByStates[$stateClassName] = $selfClassName;

                continue;
            }

            //Register it
            $loadedStatesList[$shortStateName] = $stateClassName;

            if (!is_subclass_of($stateClassName, StateInterface::class, true)) {
                throw new StateNotFound("Error, $stateClassName is not a " . StateInterface::class);
            }

            //Load and Register
            $this->registerState(
                $stateClassName,
                new $stateClassName($enablePrivateMode, $selfClassName),
                $selfClassName
            );

            //If the state is the default
            if ($shortStateName === ProxyInterface::DEFAULT_STATE_NAME) {
                $this->enableState($stateClassName);
            }
        }
    }

    /**
     * To initialize the proxy instance with all declared states. This method fetch all states defined for this class,
     * (states returned by the attribute #[Teknoo\States\Attributes\StateClass()] or via the deprecated method
     * `statesListDeclaration()`), but checks also parent's states by calling theirs StateClass attributes and static
     * methods `statesListDeclaration`.
     *
     * @throws Exception\StateNotFound
     */
    private function loadStates(): ProxyInterface
    {
        $currentClassName = static::class;

        if (isset(self::$loadedStatesCaches[$currentClassName])) {
            [
                $this->activesStates,
                $this->states,
                $this->classesByStates,
                $this->statesAliasesList,
            ] = self::$loadedStatesCaches[$currentClassName];

            return $this;
        }

        /** @var array<string> $loadedStatesList */
        $loadedStatesList = [];

        /**
         * @param array<string> &$loadedStatesList
         */
        $initializesStates = function (string $className, bool $privateMode, array &$loadedStatesList): void {
            /** @var class-string<self> $className */
            /** @var array<string> $loadedStatesList */


            $attributeStatesList = [];

            $reflectionClass = new ReflectionClass($className);
            foreach ($reflectionClass->getAttributes(StateClass::class) as $attribute) {
                /** @var ReflectionAttribute<StateClass> $attribute */
                $attributeStatesList[] = $attribute->newInstance()->getClassNames();
            }

            $methodStatesList = [];
            if (
                is_callable([$className, 'statesListDeclaration'])
                && $reflectionClass->hasMethod('statesListDeclaration')
            ) {
                $rfm = new ReflectionMethod($className, 'statesListDeclaration');
                if ($rfm->getDeclaringClass()->getName() === $className) {
                    /** @var class-string[] $methodStatesList */
                    $methodStatesList = $className::statesListDeclaration();
                }
            }

            if ([] !== $methodStatesList && is_array($methodStatesList)) {
                trigger_error(
                    "Since teknoo/states 7.1.0, Method '{$className}::statesListDeclaration()' is deprecated, "
                        . "use instead PHP attribute #[StateClass]",
                    E_USER_DEPRECATED,
                );
            }

            $statesList = array_unique(array_merge($methodStatesList, ...$attributeStatesList));

            if (empty($statesList)) {
                return;
            }

            //Private mode is only enabled for states directly defined in this stated class.
            $this->initializeStates(
                statesList: $statesList,
                enablePrivateMode: $privateMode,
                selfClassName: $className,
                loadedStatesList: $loadedStatesList
            );
        };

        $initializesStates($currentClassName, false, $loadedStatesList);

        $parentClassName = $this::class;
        do {
            $parentClassName = get_parent_class($parentClassName);
            if (
                false !== $parentClassName
                && is_subclass_of($parentClassName, ProxyInterface::class)
            ) {
                /** @var array<string> $loadedStatesList */
                $initializesStates($parentClassName, true, $loadedStatesList);
            }
        } while (false !== $parentClassName);

        self::$loadedStatesCaches[$currentClassName] = [
            $this->activesStates,
            $this->states,
            $this->classesByStates,
            $this->statesAliasesList,
        ];

        return $this;
    }

    /**
     * To test if the identifier is an non empty string and a valif full qualified class/interface name.
     *
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     * @throws Exception\StateNotFound when the state class name does not exist
     */
    protected function &validateName(string &$name): string
    {
        if (empty($name)) {
            throw new Exception\IllegalName('Error, the identifier is not a valid string');
        }

        if (!class_exists($name) && !interface_exists($name)) {
            throw new Exception\StateNotFound("Error, the state $name is not available");
        }

        if (isset($this->statesAliasesList[$name])) {
            return $this->statesAliasesList[$name];
        }

        return $name;
    }

    /**
     * Method to call into the constructor to initialize proxy's vars.
     * Externalized from the constructor to allow developers to write their own constructors into theirs classes.
     *
     * @throws Exception\StateNotFound
     */
    protected function initializeStateProxy(): void
    {
        //Initialize internal vars
        $this->states = [];
        $this->activesStates = [];
        $this->classesByStates = [];
        $this->statesAliasesList  = [];
        $this->callersStack = [];
        $this->clearCalledMethodCache();
        //Creates
        $this->loadStates();
    }

    /**
     * To compute the visibility scope from the object instance of the caller.
     *
     * Called from another class (not a child class), via a static method or an instance of this class : Public scope
     * Called from a child class, via a static method or an instance of this class : Protected scope
     * Called from a static method of this stated class, or from a method of this stated class (but not this instance) :
     *  Private scope
     * Called from a method of this stated class instance : Private state
     */
    private function extractVisibilityScopeFromObject(object &$callerObject): Visibility
    {
        if ($this === $callerObject) {
            //It's me ! Mario ! So Private scope
            return Visibility::Private;
        }

        if ($this::class === $callerObject::class) {
            //It's a brother (another instance of this same stated class, not a child), So Private scope too
            return Visibility::Private;
        }

        if ($callerObject instanceof $this) {
            //It's a child class, so Protected.
            return Visibility::Protected;
        }

        //All another case (not same class), public scope
        return Visibility::Public;
    }

    /**
     * To compute the visibility scope from the class name of the caller :.
     *
     * Called from a child class, via a static method or an instance of this class : Protected scope
     * Called from a static method of this stated class, or from a method of this stated class (but not this instance)
     *  Private scope
     */
    private function extractVisibilityScopeFromClass(string &$callerName): Visibility
    {
        $thisClassName = $this::class;

        if (is_subclass_of($callerName, $thisClassName, true)) {
            //It's a child class, so protected scope
            return Visibility::Protected;
        }

        if (is_a($callerName, $thisClassName, true)) {
            //It's this class, so private scope
            return Visibility::Private;
        }

        //All another case (not same class), public scope
        return Visibility::Public;
    }

    /**
     * To update the limit to use getVisibilityScope when the ProxyTrait is extended
     */
    private function alterVisibilityScopeLimit(int $limit): int
    {
        return $limit;
    }

    /**
     * To determine the caller visibility scope to not grant to call protected or private method from an external
     * object.
     * getVisibilityScope() uses debug_backtrace() to get last entries in the calling stack.
     *  (PHP does not provide a method to get this, but the cost of to call the debug_backtrace is very light).
     * This method is used to restore the default PHP's behavior, skipped with __call() method : PHP is naturally not
     * able to detect it : because __call, like all class's methods can access to all private and protected methods.
     *
     * Called from the main block : Public scope
     * Called from a global function : Public scope
     * Called from another class (not a child class), via a static method or an instance of this class : Public scope
     * Called from a child class, via a static method or an instance of this class : Protected scope
     * Called from a static method of this stated class, or from a method of this stated class (but not this instance) :
     *  Private scope
     * Called from a method of this stated class instance : Private state
     */
    private function getVisibilityScope(int $limit): Visibility
    {
        $limit = $this->alterVisibilityScopeLimit($limit);

        //The caller's class must only depend on the current call, never on a previous one : without this reset,
        //a caller without object (static method, function, main script) inherits the class of the previous caller
        //and can be granted to call private methods of a parent stated class.
        $this->defaultCallerStatedClassName = '';

        //Get the calling stack
        $callingStack = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, $limit);

        if (isset($callingStack[2]['function']) && '__call' !== $callingStack[2]['function']) {
            //Magic method __call adds a line into calling stack, but not other magic method
            --$limit;
        }

        if (count($callingStack) >= $limit) {
            //If size of the calling stack is less : called from main php file, or corrupted stack :
            //apply default behavior : Public
            $callerLine = array_pop($callingStack);

            if (!empty($callerLine['object']) && is_object($callerLine['object']) && isset($callerLine['class'])) {
                //It is an object
                $callerObject = $callerLine['object'];

                $this->defaultCallerStatedClassName = $callerLine['class'];
                return $this->extractVisibilityScopeFromObject($callerObject);
            }

            if (
                !empty($callerLine['class'])
                && is_string($callerLine['class'])
                && class_exists($callerLine['class'], false)
            ) {
                //It is a class
                $callerName = $callerLine['class'];

                $this->defaultCallerStatedClassName = $callerName;
                return $this->extractVisibilityScopeFromClass($callerName);
            }
        }

        //All another case (not same class), public
        //Info, If Calling stack is corrupted or in unknown state (the stack's size is less than the excepted size),
        //use default method : public
        return Visibility::Public;
    }

    /**
     * @throws Exception\StateNotFound
     */
    public function __clone()
    {
        $this->cloneProxy();
    }

    /**
     * Helper to clone proxy's values, callable easily if the Proxy class implements it's own
     * __clone() method without do a conflict traits resolution / renaming.
     *
     * @throws Exception\StateNotFound
     */
    public function cloneProxy(): ProxyInterface
    {
        //A proxy can be cloned during the execution of a state's method : the stack of callers is then not empty.
        //These callers are only callers of the original proxy, a clone must never keep them : else, all its next
        //callers are considered as the stated class owning this method, and are granted to call its private methods.
        $this->callersStack = [];

        //Clone states stack
        if (!empty($this->states)) {
            $clonedStatesArray = [];
            foreach ($this->states as $key => $state) {
                //Clone each stated class instance
                $clonedState = clone $state;
                //Update new stack
                $clonedStatesArray[$key] = $clonedState;
            }

            $this->states = $clonedStatesArray;
        }

        //Enabling states
        $this->clearCalledMethodCache();
        if (!empty($this->activesStates)) {
            $activesStates = array_keys($this->activesStates);
            $this->activesStates = [];
            foreach ($activesStates as $stateName) {
                $this->enableState((string) $stateName);
            }
        }

        return $this;
    }

    /***********************
     *** States Management *
     ***********************/

    /**
     * @throws Exception\StateNotFound
     */
    public function registerState(
        string $stateName,
        StateInterface $stateObject,
        string $originalClassName = ''
    ): ProxyInterface {
        $stateName = $this->validateName($stateName);

        if (!$stateObject instanceof $stateName) {
            throw new Exception\IllegalName(
                "Error, the state does not implement the class or interface '$stateName'"
            );
        }

        $this->states[$stateName] = $stateObject;

        if (empty($originalClassName)) {
            $originalClassName = $this::class;
        }

        $this->classesByStates[$stateName] = $originalClassName;

        if (isset($this->activesStates[$stateName])) {
            //The state is already enabled : the new instance replaces immediately the old one
            $this->activesStates[$stateName] = $stateObject;
            $this->clearCalledMethodCache();
        }

        return $this;
    }

    /**
     * @throws Exception\StateNotFound
     */
    public function unregisterState(string $stateName): ProxyInterface
    {
        $stateName = $this->validateName($stateName);

        if (!isset($this->states[$stateName])) {
            throw new Exception\StateNotFound("State '$stateName' is not available");
        }

        unset($this->states[$stateName]);

        if (isset($this->activesStates[$stateName])) {
            unset($this->activesStates[$stateName]);
            $this->clearCalledMethodCache();
        }

        if (isset($this->classesByStates[$stateName])) {
            unset($this->classesByStates[$stateName]);
        }

        return $this;
    }

    /**
     * @throws Exception\StateNotFound
     */
    public function switchState(string $stateName): ProxyInterface
    {
        $stateName = $this->validateName($stateName);

        $this->disableAllStates();
        $this->enableState($stateName);

        return $this;
    }

    /**
     * Called at each change in actives states
     */
    private function clearCalledMethodCache(): void
    {
        $this->calledMethodCache = [];
    }

    /**
     * @throws Exception\StateNotFound
     */
    public function enableState(string $stateName): ProxyInterface
    {
        $stateName = $this->validateName($stateName);

        if (isset($this->states[$stateName])) {
            $this->activesStates[$stateName] = $this->states[$stateName];
            $this->clearCalledMethodCache();
        } else {
            throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * @throws Exception\StateNotFound
     */
    public function disableState(string $stateName): ProxyInterface
    {
        $stateName = $this->validateName($stateName);

        if (isset($this->activesStates[$stateName])) {
            unset($this->activesStates[$stateName]);
            $this->clearCalledMethodCache();
        } else {
            throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    public function disableAllStates(): ProxyInterface
    {
        $this->activesStates = [];
        $this->clearCalledMethodCache();

        return $this;
    }


    /**
     * @return array<string>
     */
    protected function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && is_array($this->activesStates)) {
            return array_keys($this->activesStates);
        }

        return [];
    }

    /**
     * To list enabled states present in the passed list, and to count states of this list. A state present several
     * times, or referenced with its name and with the name of a state it overloads, is counted once.
     *
     * @param array<string> $enabledStatesList
     * @param array<string> $statesNames
     * @return array{0: array<string, true>, 1: int}
     * @throws Exception\StateNotFound
     */
    private function statesIntersect(array $enabledStatesList, array $statesNames, bool $stopAtFirstMatch): array
    {
        $inStates = [];
        $requestedStates = [];
        $list = array_flip($enabledStatesList);

        foreach ($statesNames as $stateName) {
            $stateName = $this->validateName($stateName);
            $requestedStates[$stateName] = true;

            if (isset($list[$stateName])) {
                $inStates[$stateName] = true;
            } else {
                foreach ($enabledStatesList as $enableStateName) {
                    if (is_subclass_of($enableStateName, $stateName)) {
                        $inStates[$stateName] = true;
                        break;
                    }
                }
            }

            if ($stopAtFirstMatch && !empty($inStates)) {
                break;
            }
        }

        return [$inStates, count($requestedStates)];
    }

    /**
     * @param array<string> $statesNames
     * @throws Exception\StateNotFound
     */
    private function checkInState(array $statesNames, callable $callback, bool $mustActive, bool $allStates): void
    {
        $enabledStatesList = $this->listEnabledStates();

        sort($enabledStatesList);

        //The search can only be stopped at the first enabled state found when a single enabled state is enough.
        //In all other cases, all states of the list must be checked to be compared to the count of listed states.
        [$inStates, $statesCount] = $this->statesIntersect(
            $enabledStatesList,
            $statesNames,
            $mustActive && !$allStates,
        );

        if (
            (((!$allStates && !empty($inStates)) || count($inStates) === $statesCount) && $mustActive)
            || ((empty($inStates) || (!$allStates && count($inStates) < $statesCount)) && !$mustActive)
        ) {
            $callback($enabledStatesList);
        }
    }

    /**
     * @param array<int|string, class-string> $statesNames
     * @throws Exception\StateNotFound
     */
    public function isInState(array $statesNames, callable $callback, bool $allRequired = false): ProxyInterface
    {
        $this->checkInState($statesNames, $callback, true, $allRequired);

        return $this;
    }

    /**
     * @param array<int|string, class-string> $statesNames
     * @throws Exception\StateNotFound
     */
    public function isNotInState(array $statesNames, callable $callback, bool $allForbidden = false): ProxyInterface
    {
        $this->checkInState($statesNames, $callback, false, $allForbidden);

        return $this;
    }

    /**
     * @param array<mixed> $arguments
     * @throws Exception\AvailableSeveralMethodImplementations
     * @throws Exception\MethodNotImplemented
     */
    public function __call(string $methodName, #[SensitiveParameter] array $arguments): mixed
    {
        //Get the visibility scope forbidden to call to a protected or private method from not allowed method
        $scopeVisibility = $this->getVisibilityScope(3);

        $activeStateFound = false;
        $returnValue = null;

        $callback = static function (&$value) use (&$returnValue, &$activeStateFound, $methodName): void {
            if (true === $activeStateFound) {
                throw new Exception\AvailableSeveralMethodImplementations(
                    "Method \"$methodName\" has several implementations in different states"
                );
            }

            $returnValue = $value;
            $activeStateFound = true;
        };

        //The caller is the stated class owning the state's method in execution, else the class found in the call stack
        $callerStatedClass = $this->defaultCallerStatedClassName;
        if ([] !== $this->callersStack) {
            $callerStatedClass = $this->callersStack[array_key_last($this->callersStack)];
        }

        $cachedStateName = $this->calledMethodCache[$callerStatedClass][$methodName] ?? null;
        if (null !== $cachedStateName && isset($this->activesStates[$cachedStateName])) {
            //Like in the search below, the stated class owning the state must be the caller of all methods called
            //by this method, else they are called with the caller of a previous method, still present in the stack.
            $cachedStateObject = $this->activesStates[$cachedStateName];
            if (!isset($this->classesByStates[$cachedStateName])) {
                throw new WrongConfiguration('Error, no original class name defined for ' . $cachedStateName);
            }

            $this->callersStack[] = $this->classesByStates[$cachedStateName];

            try {
                $cachedStateObject->executeClosure(
                    $this,
                    $methodName,
                    $arguments,
                    $scopeVisibility,
                    $callerStatedClass,
                    $callback
                );
            } finally {
                //Restore stated class name stack
                array_pop($this->callersStack);
            }

            if (true === $activeStateFound) {
                return $returnValue;
            }
        }

        //browse all enabled state to find the method
        $stateNameToCache = null;
        $activesStatesStack = $this->activesStates;
        foreach ($activesStatesStack as $stateName => $activeStateObject) {
            //The state is identified by the name used to register it and not by its class name : a state can be
            //registered with the name of an interface it implements (mandatory for states defined with anonymous
            //classes).
            if (!isset($this->classesByStates[$stateName])) {
                throw new WrongConfiguration('Error, no original class name defined for ' . $stateName);
            }

            $this->callersStack[] = $this->classesByStates[$stateName];

            //Call it
            try {
                $activeStateObject->executeClosure(
                    $this,
                    $methodName,
                    $arguments,
                    $scopeVisibility,
                    $callerStatedClass,
                    $callback
                );
            } finally {
                //Restore stated class name stack
                array_pop($this->callersStack);
            }

            if (true === $activeStateFound && null === $stateNameToCache) {
                $stateNameToCache = $stateName;
            }
        }

        if (true === $activeStateFound) {
            if (
                null !== $stateNameToCache
                && '' !== $methodName
                && '' !== $callerStatedClass
                && $activesStatesStack === $this->activesStates
            ) {
                //A called method is kept into the cache only if actives states are the same (same states, same
                //instances, same order) before and after its execution : a state's method can change states itself,
                //or call another method doing it, and must not be kept when its state is not enabled anymore.
                //The proxy must not keep any value depending on its history (like a counter of changes) to detect
                //it : two proxies with same states must stay equal.
                $this->calledMethodCache[$callerStatedClass][$methodName] = $stateNameToCache;
            }

            return $returnValue;
        }

        throw new Exception\MethodNotImplemented(
            sprintf('Method "%s" is not available with actives states', $methodName)
        );
    }
}
