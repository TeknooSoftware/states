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

use \UniAlteri\States\Proxy;
use \UniAlteri\States\DI;

/**
 * Interface FactoryInterface
 * @package UniAlteri\States\Factory
 * Interface to define "stated object" factory to use with this library to build a new instance
 * of a stated class.
 */
interface FactoryInterface
{
    const OBJECT_LOADER_KEY = 'objectLoader';

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container);

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * Method called by the Loader to initialize the stated class :
     *  Extends the proxy used by this stated class a child called like the stated class.
     *  => To allow developer to build new object with the operator new
     *  => To allow developer to use the operator "instanceof"
     * @param string $statedClassName the name of the stated class
     * @return boolean
     */
    public function initialize($statedClassName);

    /**
     * Initialize a proxy object with its container and states
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     * @throws Exception\IllegalProxy if the proxy object does not implement the interface
     */
    public function startup($proxyObject, $stateName=null);

    /**
     * Build a new instance of an object
     * @param mixed $arguments
     * @param string $stateName to build an object with a specific class
     * @return Proxy\ProxyInterface
     * @throws Exception\StateNotFound if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader if any loader are available for this stated class
     */
    public function build($arguments=null, $stateName=null);
}