<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Factory
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\States\Factory;

use \UniAlteri\States\DI;
use \UniAlteri\States\Loader;
use \UniAlteri\States\Proxy;

/**
 * Trait TraitFactory
 * Standard implementation of the "stated object" factory to use with this library to build a new instance
 * of a stated class.
 *
 * It is a trait to allow developer to write theirs owns factory, extendable from any class.
 *
 * @package     States
 * @subpackage  Factory
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait TraitFactory
{
    /**
     * DI Container to use with this factory object
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Finder used by this factory to load states and proxy for this stated class
     * @var Loader\FinderInterface
     */
    protected $_finder = null;

    /**
     * The stated class name used with this factory
     * @var string
     */
    protected $_statedClassName = null;

    /**
     * The path of the stated class
     * @var string
     */
    protected $_path = null;

    /**
     * To register a DI container for this object
     * @param  DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * To return the loader of this stated class from its DI Container
     * @return Loader\FinderInterface
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function getFinder()
    {
        if (!$this->_finder instanceof Loader\FinderInterface) {
            if (!$this->_diContainer instanceof DI\ContainerInterface) {
                throw new Exception\UnavailableDIContainer('Error, there are no available Di Container');
            }

            if (false === $this->_diContainer->testEntry(Loader\FinderInterface::DI_FINDER_SERVICE)) {
                throw new Exception\UnavailableLoader('Error, the finder is not available for this factory');
            }

            $this->_finder = $this->_diContainer->get(Loader\FinderInterface::DI_FINDER_SERVICE);
            if (!$this->_finder instanceof Loader\FinderInterface) {
                throw new Exception\UnavailableLoader(
                    'Error, the service does not return a finder object for this factory'
                );
            }
        }

        return $this->_finder;
    }

    /**
     * To return the stated class name used with this factory
     * @return string
     */
    public function getStatedClassName()
    {
        return $this->_statedClassName;
    }

    /**
     * To return the path of the stated class
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Method called by the Loader to initialize the stated class :
     * It registers the class name and its path, retrieves the DI Container,
     * register the factory in the DI Container, it retrieves the finder object and load the proxy
     * from the finder.
     * @param  string                           $statedClassName the name of the stated class
     * @param  string                           $path            of the stated class
     * @return boolean
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function initialize($statedClassName, $path)
    {
        //Initialize this factory
        $this->_statedClassName = $statedClassName;
        $this->_path = $path;

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
    }

    /**
     * To initialize a proxy object with its container and states. States are fetched by the finder of this stated class.
     * @param  Proxy\ProxyInterface             $proxyObject
     * @param  string                           $stateName
     * @return boolean
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function startup($proxyObject, $stateName=null)
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\IllegalProxy('Error, the Proxy does not implements the Proxy Interface');
        }

        $diContainerObject = clone $this->getDIContainer();
        $proxyObject->setDIContainer($diContainerObject);

        //Get all states available
        $finderLoader = $this->getFinder();
        $statesList = $finderLoader->listStates();

        //Check if the default state is available
        if ($statesList instanceof \ArrayObject) {
            $statesList = $statesList->getArrayCopy();
        }
        $statesList = array_combine($statesList, $statesList);
        $defaultStatedName = Proxy\ProxyInterface::DEFAULT_STATE_NAME;
        if (!isset($statesList[$defaultStatedName])) {
            throw new Exception\StateNotFound(
                'Error, the state "'.$defaultStatedName.'" was not found in this stated class'
            );
        }

        //Check if the require state is available
        if (null !== $stateName && !isset($statesList[$stateName])) {
            throw new Exception\StateNotFound('Error, the state "'.$stateName.'" was not found in this stated class');
        }

        //Load each state into proxy
        foreach ($statesList as $loadingStateName) {
            $stateObject = $finderLoader->buildState($loadingStateName);
            $stateObject->setDIContainer($diContainerObject);
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
     * Build a new instance of an object
     * @param  mixed                            $arguments
     * @param  string                           $stateName to build an object with a specific class
     * @return Proxy\ProxyInterface
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function build($arguments=null, $stateName=null)
    {
        //Get finder loader
        $finderLoader = $this->getFinder();

        //Build a new proxy object
        $proxyObject = $finderLoader->buildProxy($arguments);

        $this->startup($proxyObject, $stateName);

        return $proxyObject;
    }
}
