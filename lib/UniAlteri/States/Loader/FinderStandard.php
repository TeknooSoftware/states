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
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;

use \UniAlteri\States\Loader\Exception;
use \UniAlteri\States\DI;
use \UniAlteri\States\States;
use \UniAlteri\States\Proxy;

/**
 * Class FinderStandard
 * @package UniAlteri\States\Loader
 * Default implementation of the finder. It is used with this library to find and load
 * from each stated class all states and the proxy
 */
class FinderStandard implements FinderInterface
{
    /**
     * Current stated class's name
     * @var string
     */
    protected $_statedClassName = null;

    /**
     * Folder/Phar of the stated class
     * @var string
     */
    protected $_pathString = null;

    /**
     * DI Container to use with this finder
     * @var DI\ContainerInterface
     */
    protected $_diContainer = null;

    /**
     * Initialize finder
     * @param string $statedClassName
     * @param string $pathString
     */
    public function __construct($statedClassName, $pathString)
    {
        $this->_statedClassName = $statedClassName;
        $this->_pathString = $pathString;
    }

    /**
     * Test if class exists
     * @param string $className
     * @param string $pathName
     * @return boolean
     */
    protected function _checkClassExists($className, $pathName)
    {
        if (!is_readable($pathName)) {
            //File not found
            return false;
        }

        //Load the file
        include_once($pathName);
        if (!class_exists($className, false)) {
            //Class not found
            return false;
        }

        //Class loaded
        return true;
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
     * @throws Exception\UnavailablePath if the states's folder is not available
     * @throws Exception\UnReadablePath if the states's folder is not readable
     */
    public function listStates()
    {
        //Check if states are stored into the standardized path
        $statesPath = $this->_pathString.DIRECTORY_SEPARATOR.FinderInterface::STATES_PATH;
        if (!is_dir($statesPath)) {
            throw new Exception\UnavailablePath('Error, the path "'.$statesPath.'" was not found');
        }

        //Check if the path is available
        $hD = @opendir($statesPath);
        if (false === $hD) {
            throw new Exception\UnReadablePath('Error, the path "'.$statesPath.'" is not available');
        }

        //Extract all states (No check class exists)
        $statesNameArray = new \ArrayObject();
        while (false !== ($file = readdir($hD))) {
            switch ($file) {
                case '.';
                case '..';
                    break;
                default:
                    if (strlen($file) - 4 == strrpos($file, '.php')) {
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
     * @return States\StateInterface
     * @throws Exception\UnReadablePath if the state file is not readable
     * @throws Exception\UnavailableState if the required state is not available
     * @throws Exception\IllegalState if the state object does not implement the interface
     */
    public function loadState($stateName)
    {
        $statePath = $this->_pathString.DIRECTORY_SEPARATOR.FinderInterface::STATES_PATH.DIRECTORY_SEPARATOR.$stateName.'.php';

        if (!is_readable($statePath)) {
            throw new Exception\UnReadablePath('Error, the state "'.$stateName.'" was not found');
        }

        include_once($statePath);
        if (!class_exists($stateName, false)) {
            throw new Exception\UnavailableState('Error, the state "'.$stateName.'" is not available');
        }

        $stateObject = new $stateName;
        if (!$stateObject instanceof States\StateInterface) {
            throw new Exception\IllegalState('Error, the state "'.$stateName.'" does not implement the interface "States\StateInterface"');
        }

        return $stateObject;
    }

    /**
     * Load and build a proxy object for the stated class
     * @param array $arguments argument for proxy
     * @return Proxy\ProxyInterface
     * @throws Exception\IllegalProxy If the proxy object does not implement Proxy/ProxyInterface
     */
    public function loadProxy($arguments=null)
    {
        //Build the class file path for the proxy (standardized into ProxyInterface)
        $proxyPath = $this->_statedClassName.DIRECTORY_SEPARATOR.FinderInterface::PROXY_FILE_NAME;
        //Build the class name
        $proxyClassName = $this->_statedClassName.FinderInterface::PROXY_SUFFIX_CLASS_NAME;

        //Check if the Stated class has its own proxy
        if (true === $this->_checkClassExists($proxyClassName, $proxyPath)) {
            //Load an instance of this proxy and test if it implements the interface ProxyInterface
            $proxyObject = new $proxyClassName($arguments);
            if ($proxyObject instanceof Proxy\ProxyInterface) {
                //Initialize the proxy and return it
                $proxyObject->setDIContainer($this->getDIContainer());
                return $proxyObject;
            }

            //Throw an error
            throw new Exception\IllegalProxy('Error, the proxy of "'.$this->_statedClassName.'" does not implement "Proxy\ProxyInterface"');
        } else {
            //The stated class has not its own proxy, reuse the standard proxy, as an alias
            class_alias('\UniAlteri\States\Proxy\Standard', $proxyClassName);
            return new $proxyClassName;
        }
    }
}