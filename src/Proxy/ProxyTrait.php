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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

use Teknoo\States\State\StateInterface;

/**
 * Trait ProxyTrait
 * Implementation of the proxy class in stated class. It is used in this library to create stated class instance.
 *
 * The proxy, by default, redirect all calls, of non defined methods in the proxy, to enabled states.
 * $this, static and self keywords in all methods the stated class instance (aka in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. Since States 3.0 has no factories or no loader : proxies embedded directly theirs
 * states' configurations. Since 3.2, states configurations must be returned by the protected method
 * statesListDeclaration(), required by this trait.
 *
 * States can be overload by children of a stated class : The overloading uses only the non qualified name.
 *
 * Since 3.0, states's methods are a builder, returning a real closure to use. The state must not use
 * the Reflection API to extract the closure (Closure from Reflection are not bindable on a new scope since 7.1).
 * States can be also an anonymous class, it's name must be defined by an interface, implementing by this state.
 *
 * Since 3.2, the library following #east programming rules, all methods designed to know the state of the object are
 * been removed. Closures are now bound (with the proxy) and called by states's managing object and not directly by
 * the proxy. Result are then injected into proxy. This behavior allows developers to call several methods before return
 * the result. (But only one result is granted).
 *
 * @see ProxyInterface
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
     * private method access.
     *
     * @var \SplStack<string>
     */
    private \SplStack $callerStatedClassesStack;

    /**
     * Default class name extracted from call stack by extractVisibilityScopeFromObject
     * @var string
     */
    private string $defaultCallerStatedClassName = '';

    /**
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
     */
    abstract protected static function statesListDeclaration(): array;

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
            $shortStateName = \ltrim(\substr($stateClassName, (int) \strrpos($stateClassName, '\\')), '\\');
            if (isset($loadedStatesList[$shortStateName])) {
                $this->statesAliasesList[$stateClassName] = $loadedStatesList[$shortStateName];
                $this->classesByStates[$stateClassName] = $selfClassName;

                continue;
            }

            //Register it
            $loadedStatesList[$shortStateName] = $stateClassName;

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
     * (states returned by `statesListDeclaration()`), but checks also parent's states by calling theirs static methods
     * `statesListDeclaration`.
     *
     * @throws Exception\StateNotFound
     */
    private function loadStates(): ProxyInterface
    {
        $currentClassName = static::class;
        $loadedStatesList = [];

        $initializesStates = function ($className, $privateMode, &$loadedStatesList) {
            $rfm = new \ReflectionMethod($className, 'statesListDeclaration');
            if ($rfm->getDeclaringClass()->getName() === $className) {
                //Private mode is only enable for states directly defined in this stated class.
                $this->initializeStates(
                    $className::statesListDeclaration(),
                    $privateMode,
                    $className,
                    $loadedStatesList
                );
            }
        };

        $initializesStates($currentClassName, false, $loadedStatesList);

        $parentClassName = \get_class($this);
        do {
            $parentClassName = \get_parent_class($parentClassName);
            if (
                false !== $parentClassName
                && \is_callable([$parentClassName, 'statesListDeclaration'])
                && \is_subclass_of($parentClassName, ProxyInterface::class)
            ) {
                $initializesStates($parentClassName, true, $loadedStatesList);
            }
        } while (false !== $parentClassName);

        return $this;
    }

    /**
     * To get the class name of the caller according to scope visibility.
     */
    private function getCallerStatedClassName(): string
    {
        if (true !== $this->callerStatedClassesStack->isEmpty()) {
            return $this->callerStatedClassesStack->top();
        }

        return $this->defaultCallerStatedClassName;
    }

    /**
     * To push in the caller stated classes name stack
     * the class of the current object.
     */
    private function pushCallerStatedClassName(StateInterface $state): ProxyInterface
    {
        $stateClass = \get_class($state);

        if (!isset($this->classesByStates[$stateClass])) {
            throw new \RuntimeException("Error, no original class name defined for $stateClass");
        }

        $this->callerStatedClassesStack->push($this->classesByStates[$stateClass]);

        return $this;
    }

    /**
     * To pop the current caller in the stated class name stack.
     */
    private function popCallerStatedClassName(): ProxyInterface
    {
        if (false === $this->callerStatedClassesStack->isEmpty()) {
            $this->callerStatedClassesStack->pop();
        }

        return $this;
    }

    /**
     * To test if the identifier is an non empty string and a valif full qualified class/interface name.
     *
     * @throws Exception\IllegalName   when the identifier is not a valid full qualified class/interface  name
     * @throws Exception\StateNotFound when the state class name does not exist
     */
    protected function validateName(string &$name): bool
    {
        if (empty($name)) {
            throw new Exception\IllegalName('Error, the identifier is not a valid string');
        }

        if (!\class_exists($name) && !\interface_exists($name)) {
            throw new Exception\StateNotFound("Error, the state $name is not available");
        }

        if (isset($this->statesAliasesList[$name])) {
            $name = $this->statesAliasesList[$name];
        }

        return true;
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
        $this->callerStatedClassesStack = new \SplStack();
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
     *
     * @param object $callerObject
     */
    private function extractVisibilityScopeFromObject(&$callerObject): string
    {
        if ($this === $callerObject) {
            //It's me ! Mario ! So Private scope
            return StateInterface::VISIBILITY_PRIVATE;
        }

        if (\get_class($this) === \get_class($callerObject)) {
            //It's a brother (another instance of this same stated class, not a child), So Private scope too
            return StateInterface::VISIBILITY_PRIVATE;
        }

        if ($callerObject instanceof $this) {
            //It's a child class, so Protected.
            return StateInterface::VISIBILITY_PROTECTED;
        }

        //All another case (not same class), public scope
        return StateInterface::VISIBILITY_PUBLIC;
    }

    /**
     * To compute the visibility scope from the class name of the caller :.
     *
     * Called from a child class, via a static method or an instance of this class : Protected scope
     * Called from a static method of this stated class, or from a method of this stated class (but not this instance)
     *  Private scope
     */
    private function extractVisibilityScopeFromClass(string &$callerName): string
    {
        $thisClassName = \get_class($this);

        if (\is_subclass_of($callerName, $thisClassName, true)) {
            //It's a child class, so protected scope
            return StateInterface::VISIBILITY_PROTECTED;
        }

        if (\is_a($callerName, $thisClassName, true)) {
            //It's this class, so private scope
            return StateInterface::VISIBILITY_PRIVATE;
        }

        //All another case (not same class), public scope
        return StateInterface::VISIBILITY_PUBLIC;
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
     *
     * @return string Return :  StateInterface::VISIBILITY_PUBLIC
     *                StateInterface::VISIBILITY_PROTECTED
     *                StateInterface::VISIBILITY_PRIVATE
     */
    private function getVisibilityScope(int $limit): string
    {
        //Get the calling stack
        $callingStack = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, $limit);

        if (isset($callingStack[2]['function']) && '__call' !== $callingStack[2]['function']) {
            //Magic method __call adds a line into calling stack, but not other magic method
            --$limit;
        }

        if (\count($callingStack) >= $limit) {
            //If size of the calling stack is less : called from main php file, or corrupted stack :
            //apply default behavior : Public
            $callerLine = \array_pop($callingStack);

            if (!empty($callerLine['object']) && \is_object($callerLine['object'])) {
                //It is an object
                $callerObject = $callerLine['object'];

                $this->defaultCallerStatedClassName = $callerLine['class'];
                return $this->extractVisibilityScopeFromObject($callerObject);
            }

            if (
                !empty($callerLine['class'])
                && \is_string($callerLine['class'])
                && \class_exists($callerLine['class'], false)
            ) {
                //It is a class
                $callerName = $callerLine['class'];

                return $this->extractVisibilityScopeFromClass($callerName);
            }
        }

        //All another case (not same class), public
        //Info, If Calling stack is corrupted or in unknown state (the stack's size is less than the excepted size),
        //use default method : public
        return StateInterface::VISIBILITY_PUBLIC;
    }

    /**
     * {@inheritdoc}
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
        if (!empty($this->activesStates)) {
            $activesStates = \array_keys($this->activesStates);
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
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function registerState(
        string $stateName,
        StateInterface $stateObject,
        string $originalClassName = ''
    ): ProxyInterface {
        $this->validateName($stateName);

        if (!\is_a($stateObject, $stateName)) {
            throw new Exception\IllegalName(
                "Error, the state does not implement the class or interface '$stateName'"
            );
        }

        $this->states[$stateName] = $stateObject;

        if (empty($originalClassName)) {
            $originalClassName = \get_class($this);
        }

        $this->classesByStates[$stateName] = $originalClassName;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function unregisterState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (!isset($this->states[$stateName])) {
            throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
        }

        unset($this->states[$stateName]);

        if (isset($this->activesStates[$stateName])) {
            unset($this->activesStates[$stateName]);
        }

        if (isset($this->classesByStates[$stateName])) {
            unset($this->classesByStates[$stateName]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function switchState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        $this->disableAllStates();
        $this->enableState($stateName);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function enableState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (isset($this->states[$stateName])) {
            $this->activesStates[$stateName] = $this->states[$stateName];
        } else {
            throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function disableState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (isset($this->activesStates[$stateName])) {
            unset($this->activesStates[$stateName]);
        } else {
            throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableAllStates(): ProxyInterface
    {
        $this->activesStates = [];

        return $this;
    }


    /**
     * @return array<string>
     */
    protected function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && \is_array($this->activesStates)) {
            return \array_keys($this->activesStates);
        }

        return [];
    }

    /**
     * @param array<string> $enabledStatesList
     * @param array<string> $statesNames
     * @param bool $allStates
     * @return array<string, true>
     * @throws Exception\StateNotFound
     */
    private function statesIntersect(array $enabledStatesList, array $statesNames, bool $allStates): array
    {
        $inStates = [];
        $list = \array_flip($enabledStatesList);

        do {
            $stateName = \current($statesNames);

            $this->validateName($stateName);

            if (isset($list[$stateName])) {
                $inStates[(string) $stateName] = true;
                continue;
            }

            foreach ($enabledStatesList as $enableStateName) {
                if (\is_subclass_of($enableStateName, $stateName)) {
                    $inStates[(string) $stateName] = true;
                    break;
                }
            }
        } while (false !== \next($statesNames) && (true === empty($inStates) || true === $allStates));

        return $inStates;
    }

    /**
     * @param array<string> $statesNames
     * @throws Exception\StateNotFound
     */
    private function checkInState(array $statesNames, callable $callback, bool $mustActive, bool $allStates): void
    {
        $enabledStatesList = $this->listEnabledStates();

        \sort($enabledStatesList);

        $inStates = $this->statesIntersect($enabledStatesList, $statesNames, $allStates);

        if (((!$allStates && !empty($inStates)) || \count($inStates) === \count($statesNames)) && $mustActive) {
            $callback($enabledStatesList);
        } elseif ((empty($inStates) || (!$allStates && \count($inStates) < \count($statesNames))) && !$mustActive) {
            $callback($enabledStatesList);
        }
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function isInState(array $statesNames, callable $callback, bool $allRequired = false): ProxyInterface
    {
        $this->checkInState($statesNames, $callback, true, $allRequired);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StateNotFound
     */
    public function isNotInState(array $statesNames, callable $callback, bool $allForbidden = false): ProxyInterface
    {
        $this->checkInState($statesNames, $callback, false, $allForbidden);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @param array<mixed> $arguments
     * @throws \Throwable
     */
    public function __call(string $methodName, array $arguments)
    {
        //Get the visibility scope forbidden to call to a protected or private method from not allowed method
        $scopeVisibility = $this->getVisibilityScope(3);

        $activeStateFound = false;
        $returnValue = null;

        $callback = function (&$value) use (&$returnValue, &$activeStateFound, $methodName) {
            if (true === $activeStateFound) {
                throw new Exception\AvailableSeveralMethodImplementations(
                    "Method \"$methodName\" has several implementations in different states"
                );
            }

            $returnValue = $value;
            $activeStateFound = true;
        };

        //browse all enabled state to find the method
        $callerStatedClass = $this->getCallerStatedClassName();
        foreach ($this->activesStates as $activeStateObject) {
            $this->pushCallerStatedClassName($activeStateObject);

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
                $this->popCallerStatedClassName();
            }
        }

        if (true === $activeStateFound) {
            return $returnValue;
        }

        throw new Exception\MethodNotImplemented(
            \sprintf('Method "%s" is not available with actives states', $methodName)
        );
    }
}
