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

/**
 * Interface StartupFactoryInterface
 * @package UniAlteri\States\Factory
 * Interface to define a factory used to initialize a stated object during in constructor.
 * This factory will only find the object's factory to forward to it the call
 */
interface StartupFactoryInterface
{
    /**
     * Find the factory to use for the new proxy object to initialize it with its container and states.
     * This method is called by the constructor of the stated object
     * @param string $factoryIdentifier of the factory to use for this object
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\InvalidArgument when $factoryIdentifier is not an object
     * @throws Exception\UnavailableFactory when the required factory was not found
     */
    public static function forwardStartup($factoryIdentifier, $proxyObject, $stateName=null);
}