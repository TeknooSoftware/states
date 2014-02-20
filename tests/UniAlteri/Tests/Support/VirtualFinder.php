<?php

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;
use UniAlteri\States\Loader;

class VirtualFinder implements Loader\FinderInterface
{
    /**
     * @var DI\Container
     */
    protected $_container = null;

    /**
     * To not return the default state
     * @var bool
     */
    public $ignoreDefaultState = false;

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_container;
    }

    /**
     * List all available state object of the stated class
     * @return string[]
     */
    public function listStates()
    {
        if (empty($this->ignoreDefaultState)) {
            return array(
                'VirtualState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'VirtualState2',
                'VirtualState3'
            );
        } else {
            return array(
                'VirtualState1',
                'VirtualState2',
                'VirtualState3'
            );
        }
    }

    /**
     * Load and build the required state object of the stated class
     * @param string $stateName
     * @return \UniAlteri\States\States\StateInterface
     */
    public function loadState($stateName)
    {
        return new $stateName;
    }

    /**
     * Load and build a proxy object of the stated class
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function loadProxy()
    {
        return new VirtualProxy();
    }
}