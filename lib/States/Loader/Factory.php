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
 * @project     States
 * @category    DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;
use \UniAlteri\States\DI;
use \UniAlteri\States;

class Factory implements  FactoryInterface{
    /**
     * @var string
     */
    protected $_pathString = null;

    /**
     * @var \UniAlteri\States\DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * @var string
     */
    protected $_statedClassName = null;

    /**
     * Initialize factory
     * @param string|null $path
     */
    public function __construct($path=null){
        $this->setStatedClassPath($path);
    }

    /**
     * @param string $className
     * @param string $pathName
     * @return boolean
     */
    protected function _checkClassExists($className, $pathName){
        if(!is_readable($pathName)){
            //File not found
            return false;
        }

        //Load the file
        include_once($pathName);
        if(!class_exists($className)){
            //Class not found
            return false;
        }

        //Class loaded
        return true;
    }

    /**
     * Configure the path of loaded class
     * @param string|null $path
     */
    public function setStatedClassPath($path=null){
        if(empty($path)){

        }

        if(!file_exists($path)){
            throw new Exception\UnavailablePath('Error, the path "'.$path.'" is not available');
        }
    }

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container){
        $this->_diContainer = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_diContainer;
    }

    /**
     * List all available state object of the stated class
     * @return string[]
     * @throws Exception\UnavailablePath if the path is not accessible
     */
    public function listStates(){
        //Check if states are stored into the standardized path
        $statesPath = $this->_pathString.DIRECTORY_SEPARATOR.FactoryInterface::STATES_PATH;
        if(!is_dir($statesPath)){
            throw new Exception\UnavailablePath('Error, the path "'.$statesPath.'" was not found');
        }

        //Check if the path is available
        $hD = opendir($statesPath);
        if(false === $hD){
            throw new Exception\UnavailablePath('Error, the path "'.$statesPath.'" is not available');
        }

        //Extract all states (No check class exists)
        $statesNameArray = new \ArrayObject();
        while (false !== ($file = readdir($hD))){
            switch($file){
                case '.';
                case '..';
                    break;
                default:
                    if(strlen($file) - 4 == strrpos($file, '.php')){
                        $stateName = substr($file, 0, -4);
                        $statesNameArray[] = $stateName;
                    }
                    break;
            }
        }

        closedir($hD);

        return $statesNameArray;
    }

    /**
     * Load and build the required state object of the stated class
     * @param string $stateName
     * @return \UniAlteri\States\States\StateInterface
     * @throws Exception\UnavailableState if the state was not found or can not be loaded
     * @throws Exception\IllegalState if the state is invalid (not implement the interface)
     */
    public function loadState($stateName){
        $statePath = $this->_pathString.DIRECTORY_SEPARATOR.FactoryInterface::STATES_PATH.DIRECTORY_SEPARATOR.$stateName.'.php';

        if(!is_readable($statePath)){
            throw new Exception\UnavailableState('Error, the state "'.$stateName.'" was not found');
        }

        include_once($statePath);
        if(!class_exists($stateName)){
            throw new Exception\UnavailableState('Error, the state "'.$stateName.'" is not available');
        }

        $stateObject = new $stateName;
        if(!$stateObject instanceof States\States\StateInterface){
            throw new Exception\IllegalState('Error, the state "'.$stateName.'" does not implement the interface "\UniAlteri\States\States\StateInterface"');
        }

        return $stateObject;
    }

    /**
     * Load and build a factory object of the stated class
     * @return \UniAlteri\States\Factory\FactoryInterface
     */
    public function loadFactory(){
        //Build the class file path for the factory (standardized into FactoryInterface)
        $factoryPath = $this->_statedClassName.DIRECTORY_SEPARATOR.FactoryInterface::FACTORY_FILE_NAME;
        //Build the class name
        $factoryClassName = $this->_statedClassName.FactoryInterface::FACTORY_SUFFIX_CLASS_NAME;

        //Check if the Stated class has its own factory
        if(true === $this->_checkClassExists($factoryClassName, $factoryPath)){
            //Load an instance of this factory and test if it implements the interface FactoryInterface
            $factoryObject = new $factoryClassName();
            if($factoryObject instanceof \UniAlteri\States\Factory\FactoryInterface){
                //Initialize the factory and return it
                $factoryObject->setDIContainer($this->getDIContainer());
                return $factoryObject;
            }

            //Throw an error
            throw new \UniAlteri\States\Exception\IllegalFactory('Error, the factory of "'.$this->_statedClassName.'" does not implement "\UniAlteri\States\Factory\FactoryInterface"');
        }
        else{
            //The stated class has not its own factory, reuse the standard factory, as an alias
            class_alias('\UniAlteri\States\Factory\Standard', $factoryClassName);
            return new $factoryClassName;
        }
    }

    /**
     * Load and build a proxy object of the stated class
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function loadProxy(){
        //Build the class file path for the proxy (standardized into ProxyInterface)
        $proxyPath = $this->_statedClassName.DIRECTORY_SEPARATOR.FactoryInterface::PROXY_FILE_NAME;
        //Build the class name
        $proxyClassName = $this->_statedClassName.FactoryInterface::PROXY_SUFFIX_CLASS_NAME;

        //Check if the Stated class has its own proxy
        if(true === $this->_checkClassExists($proxyClassName, $proxyPath)){
            //Load an instance of this proxy and test if it implements the interface ProxyInterface
            $proxyObject = new $proxyClassName();
            if($proxyObject instanceof \UniAlteri\States\Proxy\ProxyInterface){
                //Initialize the proxy and return it
                $proxyObject->setDIContainer($this->getDIContainer());
                return $proxyObject;
            }

            //Throw an error
            throw new \UniAlteri\States\Exception\IllegalProxy('Error, the proxy of "'.$this->_statedClassName.'" does not implement "\UniAlteri\States\Proxy\ProxyInterface"');
        }
        else{
            //The stated class has not its own proxy, reuse the standard proxy, as an alias
            class_alias('\UniAlteri\States\Proxy\Standard', $proxyClassName);
            return new $proxyClassName;
        }
    }
}