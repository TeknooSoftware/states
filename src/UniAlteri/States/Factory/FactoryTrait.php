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
     * DI Container to use with this factory object.
     *
     * @var DI\ContainerInterface
     */
    protected $diContainer = null;

    /**
     * Finder used by this factory to load states and proxy for this stated class.
     *
     * @var Loader\FinderInterface
     */
    protected $finder = null;

    /**
     * The stated class name used with this factory.
     *
     * @var string
     */
    protected $statedClassName = null;

    /**
     * The path of the stated class.
     *
     * @var string
     */
    protected $path = null;

    /**
     * To list states by stated classes (this class and its parents).
     *
     * @var \ArrayObject
     */
    protected $statesByClassesList = null;

    /**
     * To register a DI container for this object.
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object.
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->diContainer;
    }

    /**
     * To return the loader of this stated class from its DI Container.
     *
     * @return Loader\FinderInterface
     *
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function getFinder()
    {
        if (!$this->finder instanceof Loader\FinderInterface) {
            if (!$this->diContainer instanceof DI\ContainerInterface) {
                throw new Exception\UnavailableDIContainer('Error, there are no available Di Container');
            }

            if (false === $this->diContainer->testEntry(Loader\FinderInterface::DI_FINDER_SERVICE)) {
                throw new Exception\UnavailableLoader('Error, the finder is not available for this factory');
            }

            $this->finder = $this->diContainer->get(Loader\FinderInterface::DI_FINDER_SERVICE);
            if (!$this->finder instanceof Loader\FinderInterface) {
                throw new Exception\UnavailableLoader(
                    'Error, the service does not return a finder object for this factory'
                );
            }
        }

        return $this->finder;
    }

    /**
     * To return the stated class name used with this factory.
     *
     * @return string
     */
    public function getStatedClassName()
    {
        return $this->statedClassName;
    }

    /**
     * To return the path of the stated class.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Method called by the Loader to initialize the stated class :
     * It registers the class name and its path, retrieves the DI Container,
     * register the factory in the DI Container, it retrieves the finder object and load the proxy
     * from the finder.
     *}.
     *
     * @param string $statedClassName the name of the stated class
     * @param string $path            of the stated class
     *
     * @return bool
     *
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function initialize($statedClassName, $path)
    {
        //Initialize this factory
        $this->statedClassName = $statedClassName;
        $this->path = $path;

        //Initialize Stated class container
        $diContainer = $this->getDIContainer();
        if ($diContainer instanceof DI\ContainerInterface) {
            $diContainer->registerInstance(FactoryInterface::DI_FACTORY_NAME, $this);
        } else {
            throw new Exception\UnavailableDIContainer('Error, the Di Container is not available');
        }

        //Initialize proxy
        $finder = $this->getFinder();
        $finder->loadProxy();

        //Proxy has been found, register its factory
        $this->registerFactoryInRepository();
    }

    /**
     * To register this factory in the factory repository to be able to retrieve it from another children factories.
     *
     * @return $this
     */
    protected function registerFactoryInRepository()
    {
        if (!empty($this->statedClassName) && $this->diContainer->testEntry(FactoryInterface::DI_FACTORY_REPOSITORY)) {
            $this->diContainer->get(FactoryInterface::DI_FACTORY_REPOSITORY)
                ->registerInstance($this->statedClassName, $this);
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
    protected function getFactoryFromStatedClassName($className)
    {
        if ($this->diContainer->testEntry(FactoryInterface::DI_FACTORY_REPOSITORY)) {
            $repositoryContainer = $this->diContainer->get(FactoryInterface::DI_FACTORY_REPOSITORY);
            if ($repositoryContainer instanceof DI\ContainerInterface && $repositoryContainer->testEntry($className)) {
                return $repositoryContainer->get($className);
            }
        }

        throw new Exception\UnavailableFactory('Error, the factory for '.$className.' is not available in repository');
    }

    /**
     * To return the list of available states (directly defined states and inherited states) for this class.
     *
     * @return string[]|Loader\FinderInterface[]
     *
     * @throws Exception\UnavailableLoader
     */
    protected function listStatesByClasses()
    {
        if (!$this->statesByClassesList instanceof \ArrayObject) {
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
     * @return bool
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function startup($proxyObject, $stateName = null)
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\IllegalProxy('Error, the Proxy does not implements the Proxy Interface');
        }

        $diContainerObject = clone $this->getDIContainer();
        $proxyObject->setDIContainer($diContainerObject);

        //Get all states available
        $statesList = $this->listStatesByClasses();

        //Check if the default state is available
        $defaultStatedName = Proxy\ProxyInterface::DEFAULT_STATE_NAME;
        if (!isset($statesList[$defaultStatedName])) {
            throw new Exception\StateNotFound(
                sprintf('Error, the state "%s" was not found in this stated class', $defaultStatedName)
            );
        }

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
            $stateObject = $finderLoader->buildState($loadingStateName);
            $stateObject->setDIContainer($diContainerObject)
                ->setPrivateMode($finderLoader !== $mainFinder)
                ->setStatedClassName($finderLoader->getStatedClassName());
            $proxyObject->registerState($loadingStateName, $stateObject);
        }

        //Switch to required state
        if (null !== $stateName) {
            $proxyObject->switchState($stateName);
        } else {
            $proxyObject->switchState($defaultStatedName);
        }
    }

    /**
     * Build a new instance of an object.
     *
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function build($arguments = null, $stateName = null)
    {
        //Get finder loader
        $finderLoader = $this->getFinder();

        //Build a new proxy object
        $proxyObject = $finderLoader->buildProxy($arguments);

        $this->startup($proxyObject, $stateName);

        return $proxyObject;
    }
}
