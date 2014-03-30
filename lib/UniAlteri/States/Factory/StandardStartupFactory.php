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

use UniAlteri\States\Proxy;

/**
 * Class StandardStartupFactory
 * @package UniAlteri\States\Factory
 *
 * Interface to define a factory used to initialize a stated object during in constructor.
 * This factory will only find the object's factory to forward to it the call
 */
class StandardStartupFactory implements StartupFactoryInterface
{
    /**
     * Registry of factory to use to initialize proxy object
     * @var FactoryInterface[]|\ArrayObject
     */
    protected static $_factoryRegistry = null;

    /**
     * Find the factory to use for the new proxy object to initialize it with its container and states.
     * This method is called by the constructor of the stated object
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\InvalidArgument when $factoryIdentifier is not an object
     * @throws Exception\UnavailableFactory when the required factory was not found
     */
    public static function forwardStartup($proxyObject, $stateName = null)
    {
        if (!$proxyObject instanceof Proxy\ProxyInterface) {
            throw new Exception\InvalidArgument('Error the proxy does not implement the Proxy\ProxyInterface');
        }

        $factoryIdentifier = get_class($proxyObject);

        if (!static::$_factoryRegistry instanceof \ArrayObject || !isset(static::$_factoryRegistry[$factoryIdentifier])) {
            throw new Exception\UnavailableFactory('Error, the factory "'.$factoryIdentifier.'" is not available');
        }

        return static::$_factoryRegistry[$factoryIdentifier]->startup($proxyObject, $stateName);
    }

    /**
     * Register a new factory object to initialize proxy objects
     * @param string $factoryIdentifier
     * @param FactoryInterface $factoryObject
     * @throws Exception\InvalidArgument when $factoryIdentifier is not a string
     * @throws Exception\IllegalFactory when $factoryObject doest not implement the interface FactoryInterface
     */
    public static function registerFactory($factoryIdentifier, $factoryObject)
    {
        if (!is_string($factoryIdentifier)) {
            throw new Exception\InvalidArgument('Error the factory identifier must be a string');
        }

        if (!static::$_factoryRegistry instanceof \ArrayObject) {
            static::$_factoryRegistry = new \ArrayObject();
        }

        if (!$factoryObject instanceof FactoryInterface) {
            throw new Exception\IllegalFactory(
                'Error, the factory object must implement the interface Factory\FactoryInterface'
            );
        }

        static::$_factoryRegistry[$factoryIdentifier] = $factoryObject;
    }

    /**
     * Reset startup registry
     */
    public static function reset()
    {
        if (static::$_factoryRegistry instanceof \ArrayObject) {
            static::$_factoryRegistry = null;
        }
    }

    /**
     * Return all registered factories
     * @return string[]|array
     */
    public static function listRegisteredFactory()
    {
        if (!static::$_factoryRegistry instanceof \ArrayObject) {
            return array();
        }

        return array_keys(static::$_factoryRegistry->getArrayCopy());
    }
}