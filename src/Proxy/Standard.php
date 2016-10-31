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
 * Default implementation of the proxy class in stated classes. Unlike previous major versions of States, It can be
 * instantiate directly, factories are not needed, States are directly defined in the proxy class in the static method
 * statesListDeclaration.
 *
 * The proxy, by default, redirect all calls, of non defined methods in the proxy, to enabled states.
 * $this, static and self keywords in all methods the stated class instance (aka in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. States 3.0 has no factories or no loader : proxies embedded directly theirs states'
 * configurations.
 *
 * States can be overload by children of a stated class : The overloading uses only the non qualified name.
 *
 * Since 3.0, states's methods are a builder, returning a real closure to use. The state must not use
 * the Reflection API to extract the closure (Closure from Reflection are not bindable on a new scope since 7.1).
 * States can be also an anonymous class, it's name must be defined by an interface, implementing by this state.
 *
 * @see ProxyInterface
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
     * Initialize the proxy by calling the method initializeProxy.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
    }

    /**
     * {@inheritdoc}
     */
    public static function statesListDeclaration(): array
    {
        return [];
    }
}
