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
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\States\Factory;

use Teknoo\States\Proxy\ProxyInterface;

/**
 * Interface StartupFactoryInterface
 * Interface to define the factory used to initialize a stated class instance during its initialization.
 * This factory mist only find the factory's instance to forward to it the call.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @api
 */
interface StartupFactoryInterface
{
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
    public static function forwardStartup(ProxyInterface $proxyObject, \string $stateName = null): FactoryInterface;
}
