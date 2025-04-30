<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

/**
 * Default implementation of the proxy class in stated classes. Unlike previous major versions of States, It can be
 * instantiate directly, factories are not needed, States are directly defined in the proxy class in the static method
 * statesListDeclaration.
 *
 * The proxy, by default, redirect all calls, of non defined methods in the proxy, to enabled states.
 * $this, static and self keywords in all methods the stated class instance (aka in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. Since States 3.0 has no factories or no loader : proxies embedded directly theirs
 * states' configurations. Since 3.2, states configurations must be returned by the protected method
 * statesListDeclaration(), required by this trait.
 *
 * States can be overload by children of a stated class : The overloading uses only the non qualified name.
 *
 * Since 3.0, states's methods are a builder, returning a real closure to use. The state must not use
 * the Reflection API to extract the closure (Closure from Reflection are not bindable on a new scope since 7.1).
 * States can be also an anonymous class, it's name must be defined by an interface, implementing by this state.
 *
 * Since 3.2, the library following #east programming rules, all methods designed to know the state of the object are
 * been removed. Closures are now bound (with the proxy) and called by states's managing object and not directly by
 * the proxy. Result are then injected into proxy. This behavior allows developers to call several methods before return
 * the result. (But only one result is granted).
 *
 * @see ProxyInterface
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class Standard implements ProxyInterface
{
    use ProxyTrait;

    /**
     * Initialize the proxy by calling the method initializeProxy.
     * @throws Exception\StateNotFound
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
    }

    /**
     * @return array<string>
     */
    protected static function statesListDeclaration(): array
    {
        return [];
    }
}
