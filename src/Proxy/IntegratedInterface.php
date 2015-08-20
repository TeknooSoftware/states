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

namespace UniAlteri\States\Proxy;

/**
 * Interface IntegratedInterface
 * Variant of ProxyInterface to define proxies classes in stated classes, fully integrated in PHP :
 * Developers can use the operator "new" to instantiate a stated class, unlike with the ProxyInterface proxy alone.
 *
 * A stated class instance is a proxy instance, configured from the stated class's factory, with different states instance.
 * The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this in all methods of the stated class instance (in proxy's method and states' methods) represent the proxy instance.
 *
 * By default, this library creates an alias with the canonical proxy class name and the stated class name
 * to simulate a real class with the stated class name.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface IntegratedInterface extends ProxyInterface
{
    /**
     * Method called by constructor of the integrated proxy to initialize it with the stated class factory,
     * forwarded by the startup factory.
     *
     * @api
     *
     * @throws Exception\IllegalFactory
     * @throws Exception\UnavailableFactory
     */
    public function initializeObjectWithFactory();
}
