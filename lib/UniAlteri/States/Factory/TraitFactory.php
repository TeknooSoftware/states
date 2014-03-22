<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Factory
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Factory;

use \UniAlteri\States\DI;
use \UniAlteri\States\Loader;
use \UniAlteri\States\Proxy;

/**
 * Trait TraitFactory
 * @package UniAlteri\States\Factory
 * Standard implementation of the "stated object" factory to use with this library to build a new instance
 * of a stated class.
 *
 * It is a trait to allow developer to write theirs owns factory, extendable from any class.
 */
trait TraitFactory
{

    /**
     * DI Container to use with this factory object
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_diContainer = $container;
        return $this;
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->_diContainer;
    }

    /**
     * Return the loader of this stated class from its DI Container
     * @return Loader\FinderInterface
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     */
    protected function _getLoader()
    {
        $finderLoader = $this->_diContainer->get(Loader\FinderInterface::DI_FINDER_NAME);
        if (!$finderLoader instanceof Loader\FinderInterface) {
            throw new Exception\UnavailableLoader('Error, the loader is not available');
        }

        return $finderLoader;
    }

    /**
     * Method called by the Loader to initialize the stated class :
     *  Extends the proxy used by this stated class a child called like the stated class.
     *  => To allow developer to build new object with the operator new
     *  => To allow developer to use the operator "instanceof"
     * @param string $statedClassName the name of the stated class
     * @return boolean
     * @todo test
     */
    public function initialize($statedClassName)
    {
        $finderLoader = $this->_diContainer->get(Loader\FinderInterface::DI_FINDER_NAME);
        $finderLoader->loadProxy();
    }

    /**
     * Initialize a proxy object with its container and states
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     * @throws Exception\IllegalProxy if the proxy object does not implement the interface
     */
    public function startup($proxyObject, $stateName=null)
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\IllegalProxy('Error, the Proxy does not implements the Proxy Interface');
        }

        $diContainerObject = clone $this->getDIContainer();
        $proxyObject->setDIContainer($diContainerObject);

        //Get all states available
        $finderLoader = $this->_getLoader();
        $statesList = $finderLoader->listStates();

        //Check if the default state is available
        $statesList = array_combine($statesList, $statesList);
        $defaultStatedName = Proxy\ProxyInterface::DEFAULT_STATE_NAME;
        if (!isset($statesList[$defaultStatedName])) {
            throw new Exception\StateNotFound('Error, the state "'.$defaultStatedName.'" was not found in this stated class');
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
     * @param mixed $arguments
     * @param string $stateName to build an object with a specific class
     * @return Proxy\ProxyInterface
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     */
    public function build($arguments=null, $stateName=null)
    {
        //Get finder loader
        $finderLoader = $this->_getLoader();

        //Build a new proxy object
        $proxyObject = $finderLoader->buildProxy($arguments);

        $this->startup($proxyObject, $stateName);

        return $proxyObject;
    }
}