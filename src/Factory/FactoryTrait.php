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

namespace UniAlteri\States\Factory;

use UniAlteri\States\DI;
use UniAlteri\States\Loader;
use UniAlteri\States\Proxy;

/**
 * Trait FactoryTrait
 * Standard implementation of the "stated object" factory to use with this library to build a new instance
 * of a stated class.
 *
 * It is a trait to allow developer to write theirs owns factory, extendable from any class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
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
     * @var Loader\FinderInterface
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
     * To list states by stated classes (this class and its parents).
     *
     * @var \ArrayObject
     */
    private $statesByClassesList;

    /**
     * Initialize factory
     * @param string $statedClassName
     * @param Loader\FinderInterface $finder
     * @param \ArrayAccess $factoryRepository
     */
    public function __construct(string $statedClassName, Loader\FinderInterface $finder, \ArrayAccess $factoryRepository)
    {
        $this->finder = $finder;
        $this->factoryRepository = $factoryRepository;
        $this->initialize($statedClassName);
    }

    /**
     * It registers the class name and its path, retrieves the DI Container,
     * register the factory in the DI Container, it retrieves the finder object and load the proxy
     * from the finder.
     * @api
     * @param string $statedClassName the name of the stated class
     *
     * @return $this
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
     * To return the loader of this stated class from its DI Container.
     * @api
     *
     * @return Loader\FinderInterface
     */
    public function getFinder(): Loader\FinderInterface
    {
        return $this->finder;
    }

    /**
     * To return the stated class name used with this factory.
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
     * @return $this
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
     * @return string[]|Loader\FinderInterface[]
     */
    private function listStatesByClasses()
    {
        if (!$this->statesByClassesList instanceof \ArrayAccess) {
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
     * To initialize a proxy object with its container and states. States are fetched by the finder of this stated class.
     *
     * @param Proxy\ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     */
    public function startup(Proxy\ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\IllegalProxy('Error, the Proxy does not implements the Proxy Interface');
        }

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
            $stateObject = $finderLoader->buildState(
                $loadingStateName,
                ($finderLoader !== $mainFinder),
                $finderLoader->getStatedClassName()
            );

            $proxyObject->registerState($loadingStateName, $stateObject);
        }

        //Switch to required state
        if (null !== $stateName) {
            $proxyObject->switchState($stateName);
        } elseif (isset($statesList[Proxy\ProxyInterface::DEFAULT_STATE_NAME])) {
            //No requiried stated name, check if the default state is available and load it
            $proxyObject->switchState(Proxy\ProxyInterface::DEFAULT_STATE_NAME);
        }

        return $this;
    }

    /**
     * Build a new instance of an object.
     * @api
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     */
    public function build($arguments = null, string $stateName = null): Proxy\ProxyInterface
    {
        //Get finder loader
        $finderLoader = $this->getFinder();

        //Build a new proxy object
        $proxyObject = $finderLoader->buildProxy($arguments);

        $this->startup($proxyObject, $stateName);

        return $proxyObject;
    }
}
