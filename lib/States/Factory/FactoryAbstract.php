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
 * to license@centurion-project.org so we can send you a copy immediately.
 *
 * @category    States
 * @package     Factory
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @package     Factory
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Factory;

class FactoryAbstract implements  FactoryInterface{

    /**
     * @var \UniAlteri\States\DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(\UniAlteri\States\DI\ContainerInterface $container){
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_diContainer;
    }

    /**
     * Return the loader of this stated class from its DI Container
     * @return \UniAlteri\States\Loader\FactoryInterface
     */
    protected function _getLoader(){
        $factoryLoader = $this->_diContainer->get(\UniAlteri\States\Loader\FactoryInterface::diFactoryName);
        if(!$factoryLoader instanceof \UniAlteri\States\Loader\FactoryInterface){
            throw new \UniAlteri\States\Exception\UnavailableLoader('Error, the loader is not available');
        }

        return $factoryLoader;
    }

    /**
     * Build a new instance of an object
     * @param mixed $arguments
     * @param string $stateName to build an object with a specific class
     * @return \UniAlteri\States\ObjectInterface
     */
    public function build($arguments=null, $stateName=null){
        //Get factory loader
        $factoryLoader = $this->_getLoader();

        //Build a new proxy object
        $proxyObject = $factoryLoader->loadProxy();
        $diContainerObject = $this->getDIContainer();

        //Get all states available
        $statesList = $factoryLoader->listStates();

        //Check if the defaut state is available
        $statesList = array_combine($statesList, $statesList);
        $defaultStatedName = \UniAlteri\States\Proxy\ProxyInterface::DefaultProxyName;
        if(!isset($statesList[$defaultStatedName])){
            throw new \UniAlteri\States\Exception\StateNotFound('Error, the state "'.$defaultStatedName.'" was not found in this stated class');
        }

        //Check if the require state is available
        if(null !== $stateName && !isset($statesList[$stateName])){
            throw new \UniAlteri\States\Exception\StateNotFound('Error, the state "'.$stateName.'" was not found in this stated class');
        }

        //Load each state into proxy
        foreach($statesList as $loadingStateName){
            $stateObject = $factoryLoader->loadState($loadingStateName);
            $stateObject->setDIContainer($diContainerObject);
            $proxyObject->registerState($loadingStateName, $stateObject);
        }

        //Switch to required state
        if(null !== $stateName){
            $proxyObject->switchState($stateName);
        }
        else{
            $proxyObject->switchState($defaultStatedName);
        }

        return $proxyObject;
    }
}