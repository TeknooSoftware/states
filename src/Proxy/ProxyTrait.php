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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Proxy;

use UniAlteri\States\DI;
use UniAlteri\States;

/**
 * Trait ProxyTrait
 * Standard implementation of the "Proxy Object".
 * It is used in this library to create stated object.
 *
 * A stated object is a proxy, configured for its stated class, with its different stated objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states' methods) points the proxy object.
 *
 * The library creates an alias with the proxy class name and this default proxy to simulate a dedicated proxy
 * to this class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait ProxyTrait
{
    /**
     * DI Container to use for this object.
     *
     * @var DI\ContainerInterface
     */
    protected $diContainer;

    /**
     * List of currently enabled states.
     *
     * @var \ArrayObject|States\States\StateInterface[]
     */
    protected $activesStates;

    /**
     * List of available states for this stated object.
     *
     * @var \ArrayObject|States\States\StateInterface[]
     */
    protected $states;

    /**
     * Stack to know the caller canonical stated class when an internal method call a parent method to forbid
     * private method access.
     *
     * @var string[]|\SplStack
     */
    protected $callerStatedClassesStack;

    /**
     * To get the class name of the caller according to scope visibility.
     *
     * @return string
     */
    protected function getCallerStatedClassName(): string
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
     * @param States\States\StateInterface $state
     *
     * @return $this
     */
    protected function pushCallerStatedClassName(States\States\StateInterface $state): ProxyInterface
    {
        $this->callerStatedClassesStack->push($state->getStatedClassName());

        return $this;
    }

    /**
     * To pop the current caller in the stated class name stack.
     *
     * @return $this
     */
    protected function popCallerStatedClassName(): ProxyInterface
    {
        if (false === $this->callerStatedClassesStack->isEmpty()) {
            $this->callerStatedClassesStack->pop();
        }

        return $this;
    }

    /**
     * Execute a method available in a state passed in args with the closure.
     *
     * @param States\States\StateInterface $state
     * @param $methodName
     * @param array  $arguments
     * @param string $scopeVisibility self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Exception
     */
    protected function callInState(
        States\States\StateInterface $state,
        string $methodName,
        array &$arguments,
        string $scopeVisibility
    ) {
        $callerStatedClassName = $this->getCallerStatedClassName();
        $this->pushCallerStatedClassName($state);

        //Method found, extract it
        $callingClosure = $state->getClosure($methodName, $this, $scopeVisibility, $callerStatedClassName);

        //Call it
        try {
            $returnValues = $callingClosure->call($this, ...$arguments);
        } catch (\Exception $e) {
            //Restore stated class name stack
            $this->popCallerStatedClassName();
            throw $e;
        }

        //Restore stated class name stack
        $this->popCallerStatedClassName();

        return $returnValues;
    }

    /**
     * Internal method to find closure required by caller to call it.
     *
     * @param string $methodName
     * @param array  $arguments  of the call
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     * @throws Exception\IllegalArgument      if the method's name is not a string
     * @throws \Exception
     */
    protected function findMethodToCall(string $methodName, array &$arguments)
    {
        //Get the visibility scope forbidden to call to a protected or private method from not allowed method
        $scopeVisibility = $this->getVisibilityScope(4);

        $callerStatedClassName = $this->getCallerStatedClassName();

        $methodsWithStatesArray = explode('Of', $methodName);
        if (1 < count($methodsWithStatesArray)) {
            //A specific state is required for this call
            $statesName = lcfirst(array_pop($methodsWithStatesArray));
            if (isset($this->activesStates[$statesName])) {
                //Get the state name
                $methodName = implode('Of', $methodsWithStatesArray);

                $activeStateObject = $this->activesStates[$statesName];
                if (true === $activeStateObject->testMethod($methodName, $scopeVisibility, $callerStatedClassName)) {
                    return $this->callInState($activeStateObject, $methodName, $arguments, $scopeVisibility);
                }
            }
        }

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
                        sprintf(
                            'Method "%s" has several implementations in different states',
                            $methodName
                        )
                    );
                }
            }
        }

        if ($activeStateFound instanceof States\States\StateInterface) {
            return $this->callInState($activeStateFound, $methodName, $arguments, $scopeVisibility);
        }

        throw new Exception\MethodNotImplemented(
            sprintf('Method "%s" is not available with actives states', $methodName)
        );
    }

    /**
     * To test if the identifier respects the pattern [a-zA-Z_][a-zA-Z0-9_\-]*.
     *
     * @param string $name
     *
     * @return bool
     *
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    protected function validateName(string $name): bool
    {
        if (1 == preg_match('#^[a-zA-Z_][a-zA-Z0-9_\-]*#iS', $name)) {
            return true;
        }

        throw new Exception\IllegalName('Error, the identifier is not a valid string');
    }

    /**
     * Initialize the proxy.
     */
    public function __construct()
    {
        $this->initializeProxy();
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
    }

    /**
     * To register a DI container for this object.
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container): ProxyInterface
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object.
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer(): DI\ContainerInterface
    {
        return $this->diContainer;
    }

    /**
     * To determine the caller visibility scope to not permit to call protected or private method from an external object.
     * Use debug_backtrace to get the calling stack.
     * (PHP does not provide a method to get this, but the cost of debug_backtrace is light).
     *
     * @param int $limit To define the caller into the calling stack
     *
     * @return string Return :  States\States\StateInterface::VISIBILITY_PUBLIC
     *                States\States\StateInterface::VISIBILITY_PROTECTED
     *                States\States\StateInterface::VISIBILITY_PRIVATE
     */
    protected function getVisibilityScope(int $limit): string
    {
        //Get the calling stack
        $callingStack = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, (int) $limit);

        if (isset($callingStack[2]['function']) && '__call' !== $callingStack[2]['function']) {
            //Magic method __call adds a line into calling stack, but not other magic method
            --$limit;
        }

        if (count($callingStack) >= $limit) {
            //If size of the calling stack is less : called from main php file, or corrupted stack :
            //apply default behavior : Public
            $callerLine = array_pop($callingStack);

            if (!empty($callerLine['object']) && is_object($callerLine['object'])) {
                //It is an object
                $callerObject = $callerLine['object'];

                if ($this === $callerObject) {
                    //It's me ! Mario ! Private
                    return States\States\StateInterface::VISIBILITY_PRIVATE;
                }

                if (get_class($this) === get_class($callerObject)) {
                    //It's a brother (another instance of a single class), Private
                    return States\States\StateInterface::VISIBILITY_PRIVATE;
                }

                if ($callerObject instanceof $this) {
                    //It's a child class, Protected
                    return States\States\StateInterface::VISIBILITY_PROTECTED;
                }

                //All another case (not same class), public
                return States\States\StateInterface::VISIBILITY_PUBLIC;
            }

            if (!empty($callerLine['class']) && is_string($callerLine['class']) && class_exists($callerLine['class'], false)) {
                //It is a class
                $callerName = $callerLine['class'];
                $thisClassName = \get_class($this);

                if (is_subclass_of($callerName, $thisClassName, true)) {
                    //It's a child class, Protected
                    return States\States\StateInterface::VISIBILITY_PROTECTED;
                }

                if (is_a($callerName, $thisClassName, true)) {
                    //It's this class, private
                    return States\States\StateInterface::VISIBILITY_PRIVATE;
                }
            }
        }

        //All another case (not same class), public
        //Info, If Calling stack is corrupted or in unknown state (the stack's size is less than the excepted size),
        //use default method : public
        return States\States\StateInterface::VISIBILITY_PUBLIC;
    }

    /**
     * Called to clone an Object.
     *
     * @return $this
     */
    public function __clone()
    {
        if ($this->diContainer instanceof DI\ContainerInterface) {
            $this->diContainer = clone $this->diContainer;
        }

        //Clone states stack
        if ($this->states instanceof \ArrayObject) {
            $clonedStatesArray = new \ArrayObject();
            foreach ($this->states as $key => $state) {
                //Clone each stated object
                $clonedState = clone $state;
                //Update new stack
                $clonedStatesArray[$key] = $clonedState;
            }
            $this->states = $clonedStatesArray;
        }

        //Enabling states
        if ($this->activesStates instanceof \ArrayObject) {
            $activesStates = array_keys($this->activesStates->getArrayCopy());
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
     * To register dynamically a new state for this object.
     *
     * @param string                       $stateName
     * @param States\States\StateInterface $stateObject
     *
     * @return $this
     *
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function registerState(string $stateName, States\States\StateInterface $stateObject): ProxyInterface
    {
        $this->validateName($stateName);

        $this->states[$stateName] = $stateObject;

        return $this;
    }

    /**
     * To remove dynamically a state from this object.
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
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
            throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * To disable all actives states and enable the required states.
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
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
     * @param $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound   if $stateName does not exist
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function enableState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (isset($this->states[$stateName])) {
            $this->activesStates[$stateName] = $this->states[$stateName];
        } else {
            throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * To disable an active state (not available for calling, but always loaded).
     *
     * @param string $stateName
     *
     * @return $this
     *
     * @throws Exception\IllegalArgument when the identifier is not a string
     * @throws Exception\StateNotFound   when the state was not found
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function disableState(string $stateName): ProxyInterface
    {
        $this->validateName($stateName);

        if (isset($this->activesStates[$stateName])) {
            unset($this->activesStates[$stateName]);
        } else {
            throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
        }

        return $this;
    }

    /**
     * To disable all actives states.
     *
     * @return $this
     */
    public function disableAllStates(): ProxyInterface
    {
        $this->activesStates = new \ArrayObject();

        return $this;
    }

    /**
     * To list all currently available states for this object.
     *
     * @return string[]
     */
    public function listAvailableStates()
    {
        if ($this->states instanceof \ArrayObject) {
            return array_keys($this->states->getArrayCopy());
        } else {
            return array();
        }
    }

    /**
     * To list all enable states for this object.
     *
     * @return string[]
     */
    public function listEnabledStates()
    {
        if ($this->activesStates instanceof \ArrayObject) {
            return array_keys($this->activesStates->getArrayCopy());
        } else {
            return array();
        }
    }

    /**
     * Check if the current entity is in the required state defined by $stateName.
     *
     * @param string $stateName
     *
     * @return bool
     *
     * @throws Exception\InvalidArgument when $stateName is not a valid string
     */
    public function inState(string $stateName): bool
    {
        $stateName = (string) $stateName;
        $enabledStatesList = $this->listEnabledStates();

        if (is_array($enabledStatesList) && !empty($enabledStatesList)) {
            //array_flip + isset is more efficient than in_array
            $stateName = strtr(strtolower($stateName), '_', '');
            $enabledStatesList = array_flip(
                array_map('strtolower', $enabledStatesList)
            );

            return isset($enabledStatesList[$stateName]);
        } else {
            return false;
        }
    }

    /*******************
     * Methods Calling *
     *******************/

    /**
     * To call a method of the Object.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     * @throws Exception\IllegalArgument      if the method's name is not a string
     */
    public function __call(string $name, array $arguments)
    {
        return $this->findMethodToCall($name, $arguments);
    }

    /**
     * To return the description of the method.
     *
     * @param string $methodName
     * @param string $stateName  : Return the description for a specific state of the object,
     *                           if null, use the current state
     *
     * @return \ReflectionMethod
     *
     * @throws Exception\StateNotFound        is the state required is not available
     * @throws Exception\InvalidArgument      where $methodName or $stateName are not string
     * @throws Exception\MethodNotImplemented when the method is not currently available
     * @throws \Exception                     to rethrows unknown exceptions
     */
    public function getMethodDescription(string $methodName, string $stateName = null): \ReflectionMethod
    {
       //Retrieve the visibility scope
        $scopeVisibility = $this->getVisibilityScope(3);
        $callerStatedClassName = $this->getCallerStatedClassName();
        try {
            if (null === $stateName) {
                //Browse all state to find the method
                foreach ($this->states as $stateObject) {
                    if ($stateObject->testMethod($methodName, $scopeVisibility, $callerStatedClassName)) {
                        return $stateObject->getMethodDescription($methodName);
                    }
                }
            }

            if (null !== $stateName && isset($this->states[$stateName])) {
                //Retrieve description from the required state
                if ($this->states[$stateName]->testMethod($methodName, $scopeVisibility, $callerStatedClassName)) {
                    return $this->states[$stateName]->getMethodDescription($methodName);
                }
            } elseif (null !== $stateName) {
                throw new Exception\StateNotFound(sprintf('State "%s" is not available', $stateName));
            }
        } catch (States\Exception\MethodNotImplemented $e) {
            throw new Exception\MethodNotImplemented(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw $e;
        }

        //Method not found
        throw new Exception\MethodNotImplemented(
            sprintf('Method "%s" is not available for this state', $methodName)
        );
    }

    /**
     * To invoke an object as a function.
     *
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __invoke(...$args)
    {
        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /*******************
     * Data Management *
     *******************/

    /**
     * To get a property of the object.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __get(string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To test if a property is set for the object.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __isset(string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To update a property of the object.
     *
     * @param string $name
     * @param string $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __set(string $name, $value)
    {
        $args = [$name, $value];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To remove a property of the object.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function __unset(string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To transform the object to a string
     * You cannot throw an exception from within a __toString() method. Doing so will result in a fatal error.
     *
     * @return mixed
     */
    public function __toString(): string
    {
        try {
            $args = [];

            return $this->findMethodToCall(__FUNCTION__, $args);
        } catch (\Exception $e) {
            return '';
        }
    }

    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     *
     * @return int
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function count(): int
    {
        $args = [];

        return (int) $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     *
     * @param string|int $offset
     *
     * @return bool
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetExists($offset)
    {
        $args = [$offset];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     *
     * @param string|int $offset
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetGet($offset)
    {
        $args = [$offset];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param string|int $offset
     * @param mixed      $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetSet($offset, $value)
    {
        $args = [$offset, $value];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Unset an offset.
     *
     * @param string|int $offset
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function offsetUnset($offset)
    {
        $args = [$offset];
        $this->findMethodToCall(__FUNCTION__, $args);
    }

    /************
     * Iterator *
     ************/

    /**
     * Returns the current element.
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function current()
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function key()
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Moves the current position to the next element.
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function next()
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Rewinds back to the first element of the Iterator.
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function rewind()
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Seeks to a given position in the iterator.
     *
     * @param int $position
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function seek($position)
    {
        $args = [$position];
        $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     *
     * @return bool
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function valid()
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Returns an external iterator.
     *
     * @return \Traversable
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function getIterator(): \Traversable
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object.
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     *
     * @return string
     */
    public function serialize(): string
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To wake up the object.
     *
     * @param string $serialized
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Exception\UnavailableState     if the required state is not available
     */
    public function unserialize($serialized)
    {
        $args = [$serialized];
        $this->findMethodToCall(__FUNCTION__, $args);
    }
}
