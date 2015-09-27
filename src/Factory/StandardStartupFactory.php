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

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Factory;

use UniAlteri\States\Proxy\ProxyInterface;

/**
 * Class StandardStartupFactory
 * Default implementation of startup factory used to initialize a stated class instance during its initialization.
 * This factory mist only find the factory's instance to forward to it the call.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License

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
    private static $factoryRegistry;

    /**
     * To find the factory to use for the new proxy object to initialize it with its container and states.
     * This method is called by the constructor of the stated class instance.
     *
     * @param ProxyInterface $proxyObject
     * @param string               $stateName
     *
     * @return FactoryInterface
     *
     * @throws Exception\UnavailableFactory when the required factory was not found
     */
    public static function forwardStartup(ProxyInterface $proxyObject, \string $stateName = null): FactoryInterface
    {
        $factoryIdentifier = get_class($proxyObject);

        if (!static::$factoryRegistry instanceof \ArrayAccess || !isset(static::$factoryRegistry[$factoryIdentifier])) {
            throw new Exception\UnavailableFactory(
                sprintf('Error, the factory "%s" is not available', $factoryIdentifier)
            );
        }

        return static::$factoryRegistry[$factoryIdentifier]->startup($proxyObject, $stateName);
    }

    /**
     * To register a new factory object to initialize proxy objects.
     * @api
     * @param string           $factoryIdentifier
     * @param FactoryInterface $factoryObject
     */
    public static function registerFactory(\string $factoryIdentifier, FactoryInterface $factoryObject)
    {
        if (!static::$factoryRegistry instanceof \ArrayAccess) {
            static::$factoryRegistry = new \ArrayObject();
        }

        static::$factoryRegistry[$factoryIdentifier] = $factoryObject;
    }

    /**
     * To reset startup registry.     *
     */
    public static function reset()
    {
        if (static::$factoryRegistry instanceof \ArrayAccess) {
            static::$factoryRegistry = null;
        }
    }

    /**
     * To return all registered factories.
     * @api
     * @return string[]|array
     */
    public static function listRegisteredFactory()
    {
        if (!static::$factoryRegistry instanceof \ArrayAccess) {
            return array();
        }

        return array_keys(static::$factoryRegistry->getArrayCopy());
    }
}
