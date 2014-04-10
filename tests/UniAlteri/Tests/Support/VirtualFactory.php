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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\DI;
use \UniAlteri\States\Factory;
use \UniAlteri\States\Factory\Exception;
use UniAlteri\States\Loader;
use \UniAlteri\States\Proxy;

class VirtualFactory implements Factory\FactoryInterface
{
    /**
     * To list initialized factory by loader
     * @var string[]
     */
    protected static $_initializedFactoryNameArray = array();

    /**
     * @var Proxy\ProxyInterface
     */
    protected $_startupProxy;

    /**
     * @var string
     */
    protected $_statedClassName = null;

    /**
     * @var string
     */
    protected $_path = null;

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
    }

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
    }

    /**
     * Return the loader of this stated class from its DI Container
     * @return Loader\FinderInterface
     * @throws Exception\UnavailableLoader if any finder are available for this stated class
     */
    public function getFinder()
    {
        return new VirtualFinder($this->_statedClassName, $this->_path);
    }

    /**
     * Return the path of the stated class
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Return the stated class name used with this factory
     * @return string
     */
    public function getStatedClassName()
    {
        return $this->_statedClassName;
    }

    /**
     * Method called by the Loader to initialize the stated class :
     *  Extends the proxy used by this stated class a child called like the stated class.
     *  => To allow developer to build new object with the operator new
     *  => To allow developer to use the operator "instanceof"
     * @param string $statedClassName the name of the stated class
     * @param string $path of the stated class
     * @return boolean
     */
    public function initialize($statedClassName, $path)
    {
        $this->_statedClassName = $statedClassName;
        $this->_path = $path;
        self::$_initializedFactoryNameArray[] = $statedClassName.':'.$path;
    }

    /**
     * Return the list of initialized factories by the loader
     * @return string[]
     */
    public static function listInitializedFactories()
    {
        return array_values(self::$_initializedFactoryNameArray);
    }

    /**
     * Build a new instance of an object
     * @param mixed $arguments
     * @param string $stateName to build an object with a specific class
     * @return Proxy\ProxyInterface
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     */
    public function build($arguments = null, $stateName = null)
    {
    }

    /**
     * Initialize a proxy object with its container and states
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     */
    public function startup($proxyObject, $stateName = null)
    {
        $this->_startupProxy = $proxyObject;
    }

    /**
     * Get the proxy called to startup it
     * @return Proxy\ProxyInterface
     */
    public function getStartupProxy()
    {
        return $this->_startupProxy;
    }
}