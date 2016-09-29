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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\States\Proxy;

/**
 * Class Standard
 * Default implementation of the proxy class in stated classes. It can not be instantiate directly by the developer,
 * it must use the factory to create a new instance of this stated class.
 * It is also used in this library to create stated class instance.
 *
 * A stated class instance is a proxy instance, configured from the stated class's factory, with different states instance.
 * The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this in all methods of the stated class instance (in proxy's method and states' methods) represent the proxy instance.
 *
 * By default, this library creates an alias with the canonical proxy class name and the stated class name
 * to simulate a real class with the stated class name.
 *
 * If a stated class has no proxy, an another alias is create from this standard proxy with the proxyless stated class name.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class Standard implements ProxyInterface
{
    use ProxyTrait;

    /**
     * Initialize the proxy.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
    }

    public static function statesListDeclaration(): array
    {
        return [];
    }
}
