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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Factory;

use Teknoo\States\Loader\FinderInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Trait FactoryTrait
 * Standard implementation of the stated class instance factory to use with this library to build a new instance
 * of stated classes.
 *
 * A trait implementation has been chosen to allow developer to write theirs owns factory, extendable from any class.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
trait FactoryTrait
{
    /**
     * Repository where all factories instances are registered
     *
     * @var \ArrayAccess
     */
    private $factoryRepository;

    /**
     * Finder used by this factory to load states and proxy for this stated class.
     *
     * @var FinderInterface
     */
    private $finder;

    /**
     * The stated class name used with this factory.
     *
     * @var string
     */
    private $statedClassName;

    /**
     * The path of the stated class.
     *
     * @var string
     */
    private $path;

    /**
     * To list states available in this stated classes (this class and its parents).
     *
     * @var \ArrayObject
     */
    private $statesByClassesList;

    /**
     * To list full class name of states
     *
     * @var string[]
     */
    private $fullStatesClassesNamesList;

    /**
     * List of state instance already built
     *
     * @var \Teknoo\States\State\StateInterface[]
     */
    private $statesInstancesList = [];

    /**
     * Initialize factory
     * @param string $statedClassName
     * @param FinderInterface $finder
     * @param \ArrayAccess $factoryRepository
     */
    public function __construct(string $statedClassName, FinderInterface $finder, \ArrayAccess $factoryRepository)
    {
        $this->finder = $finder;
        $this->factoryRepository = $factoryRepository;
        $this->initialize($statedClassName);
    }

    /**
     * It registers the class name in the factory, it retrieves the finder object and load the proxy from the finder.
     *
     * @param string $statedClassName the name of the stated class
     *
     * @return FactoryInterface
     */
    protected function initialize(string $statedClassName): FactoryInterface
    {
        //Initialize this factory
        $this->statedClassName = $statedClassName;

        //Initialize proxy
        $finder = $this->getFinder();
        $finder->loadProxy();

        //Proxy has been found, register its factory
        $this->registerFactoryInRepository();

        return $this;
    }

    /**
     * To return the loader of the current stated class
     * @api
     *
     * @return FinderInterface
     */
    public function getFinder(): FinderInterface
    {
        return $this->finder;
    }

    /**
     * To return the stated class name used with the factory.
     * @api
     * @return string
     */
    public function getStatedClassName(): string
    {
        return $this->statedClassName;
    }

    /**
     * To register this factory in the factory repository to be able to retrieve it from another children factories.
     *
     * @return FactoryInterface
     */
    private function registerFactoryInRepository(): FactoryInterface
    {
        if (!empty($this->statedClassName)) {
            $this->factoryRepository[$this->statedClassName] =  $this;
        }

        return $this;
    }

    /**
     * To search and return, from the factory repository, the factory for the passed class name.
     *
     * @param string $className
     *
     * @return FactoryInterface
     *
     * @throws Exception\UnavailableFactory when the required factory is not available
     */
    private function getFactoryFromStatedClassName(string $className): FactoryInterface
    {
        if (isset($this->factoryRepository[$className])) {
            return $this->factoryRepository[$className];
        }

        throw new Exception\UnavailableFactory('Error, the factory for '.$className.' is not available in repository');
    }

    /**
     * To return the list of available states (directly defined states and inherited states) for this class.
     *
     * @return string[]|FinderInterface[]
     */
    private function listStatesByClasses()
    {
        if (!$this->statesByClassesList instanceof \ArrayAccess) {
            //Compute the list of states at first call
            $statesByClassesList = new \ArrayObject();

            //Get all states directly available for this class
            $finderLoader = $this->getFinder();
            foreach ($finderLoader->listStates() as $stateName) {
                $statesByClassesList[$stateName] = $finderLoader;
            }

            //Get all available parent for this class
            foreach ($finderLoader->listParentsClassesNames() as $parentClassName) {
                $factoryInstance = $this->getFactoryFromStatedClassName($parentClassName);
                //Browse directly available state for this parent class
                //If there are not already overloaded in the current state, put it in the list
                $parentFinder = $factoryInstance->getFinder();
                foreach ($parentFinder->listStates() as $stateName) {
                    if (!isset($statesByClassesList[$stateName])) {
                        //Register parent finder to be able to load it with the good finder
                        $statesByClassesList[$stateName] = $parentFinder;
                    }
                }
            }

            $this->statesByClassesList = $statesByClassesList;
        }

        return $this->statesByClassesList;
    }

    /**
     * To list full class name of states
     *
     * @return string[]
     */
    private function getFullStateClassNameList()
    {
        if (empty($this->fullStatesClassesNamesList)) {
            $fullStateClassNameList = [];

            foreach ($this->listStatesByClasses() as $stateName => $finderLoader) {
                $className = $finderLoader->getStatedClassName() . '\\' . FinderInterface::STATES_PATH . '\\' . $stateName;
                $fullStateClassNameList[$className] = $className;
            }

            $this->fullStatesClassesNamesList = $fullStateClassNameList;
        }

        return $this->fullStatesClassesNamesList;
    }

    /**
     * Return the list of available alias for a state
     *
     * @param string $loadingStateName
     * @param FinderInterface $finderLoader
     *
     * @return string[]
     */
    private function computeStateAlias(string $loadingStateName, FinderInterface $finderLoader): array
    {
        $parentStateClassNameList = [];

        $fullStateClassName = $this->getFullStateClassNameList();

        foreach ($finderLoader->getStateParentsClassesNamesList($loadingStateName) as $className) {
            if (isset($fullStateClassName[$className])) {
                $classNameParts = explode('\\', $className);

                $parentStateClassNameList[] = array_pop($classNameParts);
            }
        }

        return $parentStateClassNameList;
    }

    /**
     * To store a single instance of each state instance, shared by all stated class instance
     * @param string $loadingStateName
     * @param FinderInterface $finderLoader
     * @param bool $enablePrivateMode
     * @return \Teknoo\States\State\StateInterface
     */
    private function buildState(string $loadingStateName, FinderInterface $finderLoader, bool $enablePrivateMode)
    {
        if (!isset($this->statesInstancesList[$loadingStateName])) {
            $this->statesInstancesList[$loadingStateName] = $finderLoader->buildState(
                $loadingStateName,
                $enablePrivateMode,
                $finderLoader->getStatedClassName(),
                $this->computeStateAlias($loadingStateName, $finderLoader)
            );
        }

        return $this->statesInstancesList[$loadingStateName];
    }

    /**
     * To initialize a proxy object with its states. States are fetched by the finder of this stated class.
     *
     * @param ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return FactoryInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     */
    public function startup(ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        //Get all states available
        $statesList = $this->listStatesByClasses();

        //Check if the require state is available
        if (null !== $stateName && !isset($statesList[$stateName])) {
            throw new Exception\StateNotFound(
                sprintf('Error, the state "%s" was not found in this stated class', $stateName)
            );
        }

        //Get the main finder of this stated class, to compare it with finders of parents classes
        $mainFinder = $this->getFinder();

        //Load each state into proxy
        foreach ($statesList as $loadingStateName => $finderLoader) {
            //Create new state object by the finder
            $stateObject = $this->buildState(
                $loadingStateName,
                $finderLoader,
                ($finderLoader !== $mainFinder) //If the finder linked to this state is not this factory's finder
                                                 // = finder come from another factory
                                                 // = state must be used in private mode
            );

            //Add the state in the proxy
            $proxyObject->registerState($loadingStateName, $stateObject);
        }

        //Switch to required state
        if (null !== $stateName) {
            $proxyObject->switchState($stateName);
        } elseif (isset($statesList[ProxyInterface::DEFAULT_STATE_NAME])) {
            //No required stated name, check if the default state is available and load it
            $proxyObject->switchState(ProxyInterface::DEFAULT_STATE_NAME);
        }

        return $this;
    }

    /**
     * Build a new instance of a stated class.
     *
     * @api
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     */
    public function build($arguments = null, string $stateName = null): ProxyInterface
    {
        //Get finder loader
        $finderLoader = $this->getFinder();

        //Build a new proxy object
        $proxyObject = $finderLoader->buildProxy($arguments);

        $this->startup($proxyObject, $stateName);

        return $proxyObject;
    }
}
