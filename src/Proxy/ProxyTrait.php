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
namespace Teknoo\States\Proxy;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\Exception\MethodNotImplemented as StateMethodNotImplemented;

/**
 * Trait ProxyTrait
 * Default implementation of the proxy class in stated class. It is used in this library to create stated class instance.
 *
 * A stated class instance is a proxy instance, configured from the stated class's factory, with different states
 * instance.  The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this in all methods of the stated class instance (in proxy's method and states' methods) represent the proxy
 * instance.
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
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
     * @var \ArrayObject|StateInterface[]
     */
    private $activesStates;

    /**
     * List of available states for this stated class instance.
     *
     * @var \ArrayObject|StateInterface[]
     */
    private $states;

    /**
     * Stack to know the caller canonical stated class when an internal method call a parent method to forbid
     * private method access.
     *
     * @var string[]|\SplStack
     */
    private $callerStatedClassesStack;

    /**
     * List all methods available in the proxy, with all states, get the list available in the current scope,
     * unlike method_exists is not dependant about the scope and return unavailable privates methods.
     *
     * @var string[]
     */
    private $globalMethodsList;

    /**
     * @var \ReflectionObject
     */
    private $thisReflection;

    /**
     * @var null|array
     */
    private $publicPropertiesList = [];

    public function __construct()
    {
        $this->loadStates();
    }

    /**
     * Method to initialize a list of states class in this proxy. Is a state have a same name of a previous loaded state
     * (from its daughters) it's skipped
     * @param array $statesList
     * @param bool $enablePrivateMode
     * @param string $selfClassName
     * @param array &$loadedStatesList
     */
    private function initializeStates(
        array $statesList,
        bool $enablePrivateMode,
        string $selfClassName,
        array &$loadedStatesList
    ) {
        foreach ($statesList as $stateClassName) {
            //Extract short class name and check if this state is not already loaded
            $shortStateName = \substr($stateClassName, \strrpos($stateClassName, '\\')+1);
            if (isset($loadedStatesList[$shortStateName])) {
                continue;
            }

            //Register it
            $loadedStatesList[$shortStateName] = $stateClassName;

            //Load and Register
            $this->registerState($stateClassName, new $stateClassName($enablePrivateMode, $selfClassName));

            //If the state is the default
            if ($shortStateName == StateInterface::STATE_DEFAULT_NAME) {
                $this->enableState($stateClassName);
            }
        }
    }

    /**
     * To initialize the proxy instance with all declared states
     *
     * @return ProxyInterface
     */
    private function loadStates(): ProxyInterface
    {
        $currentClassName = static::class;
        $loadedStatesList = [];

        $this->initializeStates(static::statesListDeclaration(), false, $currentClassName, $loadedStatesList);

        $parentClassName = \get_class($this);
        do {
            $parentClassName = \get_parent_class($parentClassName);
            if (\class_exists($parentClassName)
                    && \is_subclass_of($parentClassName, ProxyInterface::class)) {

                $this->initializeStates(
                    $parentClassName::statesListDeclaration(),
                    true,
                    $parentClassName,
                    $loadedStatesList
                );
            }
        } while (false !== $parentClassName);

        return $this;
    }

    /**
     * List all methods available in the proxy, with all states, get the list available in the current scope,
     * unlike method_exists is not dependant about the scope and return unavailable privates methods.
     *
     * @return array|string[]
     */
    private function getGlobalMethodsList()
    {
        if (null === $this->globalMethodsList) {
            $this->globalMethodsList = \array_flip(\get_class_methods($this));
        }

        return $this->globalMethodsList;
    }

    /**
     * Check if a method exist in the current scope for this proxy.
     * unlike method_exists is not dependant about the scope and return unavailable privates methods
     * unlike is_callable, this check is not influenced by __call().
     *
     * @param string $methodName
     *
     * @return bool
     */
    private function checkMethodExist(string $methodName)
    {
        return isset($this->getGlobalMethodsList()[$methodName]);
    }

    /**
     * To get the class name of the caller according to scope visibility.
     *
     * @return string
     */
    private function getCallerStatedClassName(): string
    {
        if (true !== $this->callerStatedClassesStack->isEmpty()) {
            return $this->callerStatedClassesStack->top();
        }

        return '';
    }

    /**
     * To push in the caller stated classes name stack
     * the class of the current object.
     *
     * @param StateInterface $state
     *
     * @return ProxyInterface
     */
    private function pushCallerStatedClassName(StateInterface $state): ProxyInterface
    {
        $this->callerStatedClassesStack->push($state->getStatedClassName());

        return $this;
    }

    /**
     * To pop the current caller in the stated class name stack.
     *
     * @return ProxyInterface
     */
    private function popCallerStatedClassName(): ProxyInterface
    {
        if (false === $this->callerStatedClassesStack->isEmpty()) {
            $this->callerStatedClassesStack->pop();
        }

        return $this;
    }

    /**
     * Prepare the execution's context and execute a method available in a state passed in args with the closure.
     *
     * @param StateInterface $state
     * @param string         $methodName
     * @param array          $arguments
     * @param string         $scopeVisibility self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    private function callInState(
        StateInterface $state,
        string $methodName,
        array &$arguments,
        string $scopeVisibility
    ) {
        $callerStatedClassName = $this->getCallerStatedClassName();
        $this->pushCallerStatedClassName($state);

        //Method found, extract it
        /**
         * @var ProxyInterface|ProxyTrait $this
         */
        $callingClosure = $state->getClosure($methodName, $scopeVisibility, $callerStatedClassName);

        //Call it
        try {
            $returnValues = $callingClosure->call($this, ...$arguments);
        } catch (\Throwable $e) {
            //Restore stated class name stack
            $this->popCallerStatedClassName();

            throw $e;
        }

        //Restore stated class name stack
        $this->popCallerStatedClassName();

        return $returnValues;
    }

    /**
     * Internal method to find, in enabled stated, the closure required by caller to call it.
     *
     * @api
     *
     * @param string $methodName
     * @param array  $arguments  of the call
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Exception
     */
    protected function findMethodToCall(string $methodName, array &$arguments)
    {
        //Get the visibility scope forbidden to call to a protected or private method from not allowed method
        $scopeVisibility = $this->getVisibilityScope(4);

        $callerStatedClassName = $this->getCallerStatedClassName();

        $activeStateFound = null;
        //No specific state required, browse all enabled state to find the method
        foreach ($this->activesStates as $activeStateObject) {
            if (true === $activeStateObject->testMethod($methodName, $scopeVisibility, $callerStatedClassName)) {
                if (null === $activeStateFound) {
                    //Check if there are only one enabled state whom implements this method
                    $activeStateFound = $activeStateObject;
                } else {
                    //Else, throw an exception
                    throw new Exception\AvailableSeveralMethodImplementations(
                        \sprintf(
                            'Method "%s" has several implementations in different states',
                            $methodName
                        )
                    );
                }
            }
        }

        if ($activeStateFound instanceof StateInterface) {
            return $this->callInState($activeStateFound, $methodName, $arguments, $scopeVisibility);
        }

        throw new Exception\MethodNotImplemented(
            \sprintf('Method "%s" is not available with actives states', $methodName)
        );
    }

    /**
     * To test if the identifier is an non empty string.
     * Convert also canonical states name (aka state's class name) to its identifier (kepp only the class name without
     * its namespace)
     *
     * @api
     *
     * @param string $name
     *
     * @return bool
     *
     * @throws Exception\IllegalName when the identifier is not an non empty string
     * @throws Exception\StateNotFound when the state class name does not exist
     */
    protected function validateName(string &$name): bool
    {
        if (empty($name)) {
            throw new Exception\IllegalName('Error, the identifier is not a valid string');
        }

        if (!\class_exists($name)) {
            throw new Exception\StateNotFound("Error, the state $name is not available");
        }

        return true;
    }

    /**
     * Method to call into the constructor to initialize proxy's vars.
     * Externalized from the constructor to allow developers to write their own constructors into theirs classes.
     */
    protected function initializeProxy()
    {
        //Initialize internal vars
        $this->states = new \ArrayObject();
        $this->activesStates = new \ArrayObject();
        $this->callerStatedClassesStack = new \SplStack();
        //Creates
        $this->loadStates();
    }

    /**
     * To determine the caller visibility scope to not grant to call protected or private method from an external object.
     * getVisibilityScope() uses debug_backtrace() to get last entries in the calling stack.
     *  (PHP does not provide a method to get this, but the cost of to call the debug_backtrace is very light).
     *
     * Called from the main block : Public scope
     * Called from a global function : Public scope
     * Called from another class (not a child class), via a static method or an instance of this class : Public scope
     * Called from a child class, via a static method or an instance of this class : Protected scope
     * Called from a static method of this stated class, or from a method of this stated class (but not this instance) :
     *  Private scope
     * Called from a method of this stated class instance : Private state
     *
     * @param int $limit To define the caller into the calling stack
     *
     * @return string Return :  StateInterface::VISIBILITY_PUBLIC
     *                StateInterface::VISIBILITY_PROTECTED
     *                StateInterface::VISIBILITY_PRIVATE
     */
    private function getVisibilityScope(int $limit): string
    {
        //Get the calling stack
        $callingStack = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, (int) $limit);

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

            if (!empty($callerLine['class'])
                && \is_string($callerLine['class'])
                && \class_exists($callerLine['class'], false)) {

                //It is a class
                $callerName = $callerLine['class'];
                $thisClassName = \get_class($this);

                if (\is_subclass_of($callerName, $thisClassName, true)) {
                    //It's a child class, so protected scope
                    return StateInterface::VISIBILITY_PROTECTED;
                }

                if (\is_a($callerName, $thisClassName, true)) {
                    //It's this class, so private scope
                    return StateInterface::VISIBILITY_PRIVATE;
                }
            }
        }

        //All another case (not same class), public
        //Info, If Calling stack is corrupted or in unknown state (the stack's size is less than the excepted size),
        //use default method : public
        return StateInterface::VISIBILITY_PUBLIC;
    }

    /**
     * Called to clone this stated class instance, clone states entities and the current state of this instance.
     *
     * @api
     *
     * @return $this
     */
    public function __clone()
    {
        //Clone states stack
        if ($this->states instanceof \ArrayAccess) {
            $clonedStatesArray = new \ArrayObject();
            foreach ($this->states as $key => $state) {
                //Clone each stated class instance
                $clonedState = clone $state;
                //Update new stack
                $clonedStatesArray[$key] = $clonedState;
            }
            $this->states = $clonedStatesArray;
        }

        //Enabling states
        if ($this->activesStates instanceof \ArrayAccess) {
            $activesStates = \array_keys($this->activesStates->getArrayCopy());
            $this->activesStates = new \ArrayObject();
            foreach ($activesStates as $stateName) {
                $this->enableState($stateName);
            }
        }

        return $this;
    }

    /***********************
     *** States Management *
     ***********************/

    /**
     * To register dynamically a new state for this stated class instance.
     *
     * @api
     *
     * @param string         $stateName
     * @param StateInterface $stateObject
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName when the identifier is not an non empty string
     */
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface
    {
        $this->validateName($stateName);

        $this->states[$stateName] = $stateObject;

        return $this;
    }

    /**
     * To remove dynamically a state from this stated class instance.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not an non empty string
     */
    public function unregisterState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (isset($this->states[$stateName])) {
            unset($this->states[$stateName]);

            if (isset($this->activesStates[$stateName])) {
                unset($this->activesStates[$stateName]);
            }
        } else {
            throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * To disable all enabled states and enable the required states.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\IllegalName when the identifier is not an non empty string
     */
    public function switchState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        $this->disableAllStates();
        $this->enableState($stateName);

        return $this;
    }

    /**
     * To enable a loaded states.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound if $stateName does not exist
     * @throws Exception\IllegalName   when the identifier is not an non empty string
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
     * To disable an enabled state.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound when the state was not found
     * @throws Exception\IllegalName   when the identifier is not an non empty string
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
     * To disable all actives states.
     *
     * @api
     *
     * @return ProxyInterface
     */
    public function disableAllStates(): ProxyInterface
    {
        $this->activesStates = new \ArrayObject();

        return $this;
    }

    /**
     * To list all currently available states for this object.
     *
     * @api
     *
     * @return string[]
     */
    public function listAvailableStates()
    {
        if ($this->states instanceof \ArrayAccess) {
            return \array_keys($this->states->getArrayCopy());
        } else {
            return [];
        }
    }

    /**
     * To list all enable states for this object.
     *
     * @api
     *
     * @return string[]
     */
    public function listEnabledStates()
    {
        if ($this->activesStates instanceof \ArrayAccess) {
            return \array_keys($this->activesStates->getArrayCopy());
        } else {
            return [];
        }
    }

    /**
     * To return the list of all states entity available for this object.
     *
     * @api
     *
     * @return \ArrayAccess|StateInterface[]
     */
    public function getStatesList()
    {
        if ($this->states instanceof \ArrayAccess) {
            return $this->states;
        }

        return new \ArrayObject();
    }

    /**
     * Check if this stated class instance is in the required state defined by $stateName.
     *
     * @api
     *
     * @param string $stateName
     *
     * @return bool
     */
    public function inState(string $stateName): bool
    {
        $this->validateName($stateName);
        $enabledStatesList = $this->listEnabledStates();

        if (\is_array($enabledStatesList) && isset(\array_flip($enabledStatesList)[$stateName])) {
            return true;
        } elseif (\is_array($enabledStatesList)) {
            foreach ($enabledStatesList as $enableStateName) {
                if (\is_subclass_of($enableStateName, $stateName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * To call a method of the this stated class instance not defined in the proxy.
     *
     * @api
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __call(string $name, array $arguments)
    {
        if (!$this->callerStatedClassesStack->isEmpty()) {
            if ($this->checkMethodExist($name)) {
                return $this->{$name}(...$arguments);
            }
        }

        return $this->findMethodToCall($name, $arguments);
    }

    /**
     * To return the description of a method present in a state of this stated class instance.
     * This method no checks if the method is available in the current scope by the called.
     *
     * @api
     *
     * @param string $methodName
     * @param string $stateName  : Return the description for a specific state of the object,
     *                           if null, use the current state
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\StateNotFound        is the state required is not available
     * @throws Exception\MethodNotImplemented when the method is not currently available
     * @throws \Throwable                     to rethrows unknown exceptions
     */
    public function getMethodDescription(string $methodName, string $stateName = null): \ReflectionMethod
    {
        //Retrieve the visibility scope
        try {
            if (null === $stateName) {
                //Browse all state to find the method
                foreach ($this->states as $stateObject) {
                    return $stateObject->getMethodDescription($methodName);
                }
            }

            if (null !== $stateName && isset($this->states[$stateName])) {
                //Retrieve description from the required state
                return $this->states[$stateName]->getMethodDescription($methodName);
            } elseif (null !== $stateName) {
                throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
            }
        } catch (StateMethodNotImplemented $e) {
            //Catch MethodNotImplemented from state entity to surround in a proxy exception
            throw new Exception\MethodNotImplemented(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Throwable $e) {
            throw $e;
        }

        //Method not found
        throw new Exception\MethodNotImplemented(
            \sprintf('Method "%s" is not available for this state', $methodName)
        );
    }
}
