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
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Loader;

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
     * Initialize factory
     */
    public function __construct(){
        $this->setStatedClassPath();
    }

    /**
     * Configure the path of loaded class
     * @param string|null $path
     */
    public function setStatedClassPath($path=null){
        if(empty($path)){

        }

        if(!file_exists($path)){
            throw new \UniAlteri\States\Exception\UnavailablePath('Error, the path "'.$path.'" is not available');
        }
    }

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
     * List all available state object of the stated class
     * @return string[]
     */
    public function listStates(){

    }

    /**
     * Load and build the required state object of the stated class
     * @param string $stateName
     * @return \UniAlteri\States\States\StateInterface
     */
    public function loadState($stateName){

    }

    /**
     * Load and build a factory object of the stated class
     * @return \UniAlteri\States\Factory\FactoryInterface
     */
    public function loadFactory(){

    }

    /**
     * Load and build a proxy object of the stated class
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function loadProxy(){

    }
}