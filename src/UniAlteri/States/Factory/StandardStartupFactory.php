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
 * @version     1.1.1
 */

namespace UniAlteri\States\Factory;

use UniAlteri\States\Proxy;

/**
 * Class StandardStartupFactory
 * Default implementation of the startup factory to define a factory used to initialize a stated object during
 * in constructor. This factory will only find the object's factory to forward to it the call.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @api
 */
class StandardStartupFactory implements StartupFactoryInterface
{
    /**
     * Registry of factory to use to initialize proxy object.
     *
     * @var FactoryInterface[]|\ArrayObject
     */
    protected static $factoryRegistry = null;

    /**
     * To find the factory to use for the new proxy object to initialize it with its container and states.
     * This method is called by the constructor of the stated object.
     *
     * @param Proxy\ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return bool
     *
     * @throws Exception\InvalidArgument    when $factoryIdentifier is not an object
     * @throws Exception\UnavailableFactory when the required factory was not found
     */
    public static function forwardStartup($proxyObject, $stateName = null)
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\InvalidArgument('Error the proxy does not implement the Proxy\ProxyInterface');
        }

        $factoryIdentifier = get_class($proxyObject);

        if (!static::$factoryRegistry instanceof \ArrayObject || !isset(static::$factoryRegistry[$factoryIdentifier])) {
            throw new Exception\UnavailableFactory(
                sprintf('Error, the factory "%s" is not available', $factoryIdentifier)
            );
        }

        return static::$factoryRegistry[$factoryIdentifier]->startup($proxyObject, $stateName);
    }

    /**
     * To register a new factory object to initialize proxy objects.
     *
     * @param string           $factoryIdentifier
     * @param FactoryInterface $factoryObject
     *
     * @throws Exception\InvalidArgument when $factoryIdentifier is not a string
     * @throws Exception\IllegalFactory  when $factoryObject doest not implement the interface FactoryInterface
     */
    public static function registerFactory($factoryIdentifier, $factoryObject)
    {
        if (!is_string($factoryIdentifier)) {
            throw new Exception\InvalidArgument('Error the factory identifier must be a string');
        }

        if (!static::$factoryRegistry instanceof \ArrayObject) {
            static::$factoryRegistry = new \ArrayObject();
        }

        if (!$factoryObject instanceof FactoryInterface) {
            throw new Exception\IllegalFactory(
                'Error, the factory object must implement the interface Factory\FactoryInterface'
            );
        }

        static::$factoryRegistry[$factoryIdentifier] = $factoryObject;
    }

    /**
     * To reset startup registry.
     */
    public static function reset()
    {
        if (static::$factoryRegistry instanceof \ArrayObject) {
            static::$factoryRegistry = null;
        }
    }

    /**
     * To return all registered factories.
     *
     * @return string[]|array
     */
    public static function listRegisteredFactory()
    {
        if (!static::$factoryRegistry instanceof \ArrayObject) {
            return array();
        }

        return array_keys(static::$factoryRegistry->getArrayCopy());
    }
}
