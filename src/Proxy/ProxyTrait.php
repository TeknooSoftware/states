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

use Teknoo\States\State\Exception\MethodNotImplemented;
use Teknoo\States\State\StateInterface;

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
     * @var StateInterface[]
     */
    private $activesStates = [];

    /**
     * List of available states for this stated class instance.
     *
     * @var StateInterface[]
     */
    private $states = [];

    /**
     * Stack to know the caller canonical stated class when an internal method call a parent method to forbid
     * private method access.
     *
     * @var string[]|\SplStack
     */
    private $callerStatedClassesStack;

    /**
     * Method to initialize a list of states class in this proxy. Is a state have a same name of a previous loaded state
     * (defined in previously in this class) it's skipped.
     *
     * @param array  $statesList
     * @param bool   $enablePrivateMode
     * @param string $selfClassName
     * @param array  &$loadedStatesList
     */
    private function initializeStates(
        array $statesList,
        bool $enablePrivateMode,
        string $selfClassName,
        array &$loadedStatesList
    ) {
        foreach ($statesList as $stateClassName) {
            //Extract short class name and check if this state is not already loaded
            $shortStateName = \substr($stateClassName, \strrpos($stateClassName, '\\') + 1);
            if (isset($loadedStatesList[$shortStateName])) {
                continue;
            }

            //Register it
            $loadedStatesList[$shortStateName] = $stateClassName;

            //Load and Register
            $this->registerState($stateClassName, new $stateClassName($enablePrivateMode, $selfClassName));

            //If the state is the default
            if ($shortStateName == ProxyInterface::DEFAULT_STATE_NAME) {
                $this->enableState($stateClassName);
            }
        }
    }

    /**
     * To initialize the proxy instance with all declared states. This method fetch all states defined for this class,
     * (states returned by `statesListDeclaration()`), but checks also parent's states.
     *
     * @return ProxyInterface
     */
    private function loadStates(): ProxyInterface
    {
        $currentClassName = static::class;
        $loadedStatesList = [];

        //Private mode is only enable for states directly defined in this stated class.
        $this->initializeStates(static::statesListDeclaration(), false, $currentClassName, $loadedStatesList);

        $parentClassName = \get_class($this);
        do {
            $parentClassName = \get_parent_class($parentClassName);
            if (\class_exists($parentClassName)
                    && \is_subclass_of($parentClassName, ProxyInterface::class)) {

                //Private mode is disable for states directly defined in parent class.
                /*
                 * @var ProxyInterface $parentClassName
                 */
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
     * Internal method to find, in enabled stated, the method/closure required by caller to call it.
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
     * its namespace).
     *
     * @param string $name
     *
     * @return bool
     *
     * @throws Exception\IllegalName   when the identifier is not an non empty string
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

        return true;
    }

    /**
     * Method to call into the constructor to initialize proxy's vars.
     * Externalized from the constructor to allow developers to write their own constructors into theirs classes.
     */
    protected function initializeProxy()
    {
        //Initialize internal vars
        $this->states = [];
        $this->activesStates = [];
        $this->callerStatedClassesStack = new \SplStack();
        //Creates
        $this->loadStates();
    }

    /**
     * To determine the caller visibility scope to not grant to call protected or private method from an external object.
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
     * {@inheritdoc}
     */
    public function __clone()
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
                $this->enableState($stateName);
            }
        }

        return $this;
    }

    /***********************
     *** States Management *
     ***********************/

    /**
     * {@inheritdoc}
     */
    public function registerState(string $stateName, StateInterface $stateObject): ProxyInterface
    {
        $this->validateName($stateName);

        if (!\is_a($stateObject, $stateName) && !\is_subclass_of($stateObject, $stateName)) {
            throw new Exception\IllegalName(
                sprintf(
                    'Error, the state does not implement the class or interface "%s"',
                    $stateName
                )
            );
        }

        $this->states[$stateName] = $stateObject;

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function listAvailableStates(): array
    {
        if (!empty($this->states) && \is_array($this->states)) {
            return \array_keys($this->states);
        } else {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && \is_array($this->activesStates)) {
            return \array_keys($this->activesStates);
        } else {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatesList() : array
    {
        if (!empty($this->states)) {
            return $this->states;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function inState(string $stateName): bool
    {
        $this->validateName($stateName);
        $enabledStatesList = $this->listEnabledStates();

        if (\is_array($enabledStatesList)) {
            if (isset(\array_flip($enabledStatesList)[$stateName])) {
                return true;
            } else {
                foreach ($enabledStatesList as $enableStateName) {
                    if (\is_subclass_of($enableStateName, $stateName)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function __call(string $name, array $arguments)
    {
        return $this->findMethodToCall($name, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription(string $methodName, string $stateName = null): \ReflectionMethod
    {
        if (!empty($stateName)) {
            $this->validateName($stateName);

            if (!isset($this->states[$stateName])) {
                throw new Exception\StateNotFound(\sprintf('State "%s" is not available', $stateName));
            }
        }

        //Browse all state to find the method
        foreach ($this->states as $stateObject) {
            if (null !== $stateName && \get_class($stateObject) !== $stateName) {
                continue;
            }

            try {
                return $stateObject->getMethodDescription($methodName);
            } catch (MethodNotImplemented $e) {
            }
        }

        //Method not found
        throw new Exception\MethodNotImplemented(
            \sprintf('Method "%s" is not available for this state', $methodName)
        );
    }
}
