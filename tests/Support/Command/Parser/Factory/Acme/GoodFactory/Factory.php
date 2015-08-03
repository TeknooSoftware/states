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
 *
 * Mock factory file to test command for cli helper
 */

namespace Acme\GoodFactory;

use UniAlteri\States\DI;
use UniAlteri\States\Factory\Exception;
use UniAlteri\States\Factory\FactoryInterface;
use UniAlteri\States\Loader;
use UniAlteri\States\Proxy;

class Factory implements FactoryInterface
{
    /**
     * Initialize factory
     * @param string $statedClassName
     * @param Loader\FinderInterface $finder
     * @param \ArrayAccess $factoryRepository
     */
    public function __construct(string $statedClassName, Loader\FinderInterface $finder, \ArrayAccess $factoryRepository)
    {

    }

    /**
     * To return the loader of this stated class from its DI Container.
     *
     * @return Loader\FinderInterface
     *
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     */
    public function getFinder(): Loader\FinderInterface
    {
    }

    /**
     * To return the stated class name used with this factory.
     *
     * @return string
     */
    public function getStatedClassName(): string
    {
    }

    /**
     * To initialize a proxy object with its container and states. States are fetched by the finder of this stated class.
     *
     * @param Proxy\ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return $this
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     */
    public function startup(Proxy\ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        return $this;
    }

    /**
     * Build a new instance of an object.
     *
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\UnavailableLoader      if any finder are available for this stated class
     */
    public function build($arguments = null, string $stateName = null): Proxy\ProxyInterface
    {
    }
}
