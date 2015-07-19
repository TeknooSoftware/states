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

use UniAlteri\States\Proxy;
use UniAlteri\States\DI;
use UniAlteri\States\Loader;

/**
 * Interface FactoryInterface
 * Interface to define "stated object" factory to use with this library to build a new instance
 * of a stated class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface FactoryInterface
{
    /**
     * Name of this factory into the DI Container available for each stated class.
     */
    const DI_FACTORY_NAME = 'Factory';

    /**
     * Name of the repository in the DI Container, shared with all DI, to register all loaded factory.
     */
    const DI_FACTORY_REPOSITORY = 'FactoryRepository';

    /**
     * To register a DI container for this object.
     * @api
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container): FactoryInterface;

    /**
     * To return the DI Container used for this object.
     * @api
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer(): DI\ContainerInterface;

    /**
     * To return the loader of this stated class from its DI Container.
     * @api
     *
     * @return Loader\FinderInterface
     *
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     */
    public function getFinder(): Loader\FinderInterface;

    /**
     * To return the stated class name used with this factory.
     * @api
     * @return string
     */
    public function getStatedClassName(): string;

    /**
     * To return the path of the stated class.
     * @api
     * @return string
     */
    public function getPath(): string;

    /**
     * Method called by the Loader to initialize the stated class :
     * It registers the class name and its path, retrieves the DI Container,
     * register the factory in the DI Container, it retrieves the finder object and load the proxy
     * from the finder.
     * @api
     * @param string $statedClassName the name of the stated class
     * @param string $path            of the stated class
     *
     * @return $this
     *
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     */
    public function initialize(string $statedClassName, string $path): FactoryInterface;

    /**
     * To initialize a proxy object with its container and states. States are fetched by the finder of this stated class.
     * @internal
     * @param Proxy\ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     */
    public function startup(Proxy\ProxyInterface $proxyObject, string $stateName = null): FactoryInterface;

    /**
     * Build a new instance of an object.
     * @api
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\UnavailableDIContainer if there are no di container
     */
    public function build($arguments = null, string $stateName = null): Proxy\ProxyInterface;
}
