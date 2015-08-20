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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Factory;

use UniAlteri\States\Proxy\ProxyInterface;
use UniAlteri\States\Loader\FinderInterface;

/**
 * Interface FactoryInterface
 * Interface to define stated class factory to use with this library to build a new instance
 * of a stated class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface FactoryInterface
{
    /**
     * Initialize factory
     * @param string $statedClassName
     * @param FinderInterface $finder
     * @param \ArrayAccess $factoryRepository
     */
    public function __construct(\string $statedClassName, FinderInterface $finder, \ArrayAccess $factoryRepository);

    /**
     * To return the loader of the current stated class
     * @api
     *
     * @return FinderInterface
     */
    public function getFinder(): FinderInterface;

    /**
     * To return the stated class name used with the factory.
     * @api
     * @return string
     */
    public function getStatedClassName(): \string;

    /**
     * To initialize a proxy object with its states. States are fetched by the finder of this stated class.
     *
     * @param ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return FactoryInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     * @throws Exception\IllegalProxy           if the proxy object does not implement the interface
     */
    public function startup(ProxyInterface $proxyObject, \string $stateName = null): FactoryInterface;

    /**
     * Build a new instance of a stated class.
     *
     * @api
     * @param mixed  $arguments
     * @param string $stateName to build an object with a specific class
     *
     * @return ProxyInterface
     *
     * @throws Exception\StateNotFound          if the $stateName was not found for this stated class
     */
    public function build($arguments = null, \string $stateName = null): ProxyInterface;
}
