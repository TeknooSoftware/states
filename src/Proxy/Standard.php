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
 * Default implementation of the proxy class in stated classes. Unlike previous major versions of States, It can not be
 * instantiate directly, factories are not needed, States are directly defined in the proxy class in the static method
 * statesListDeclaration.
 *
 * The proxy, by default, redirect all calls, on non defined methods in the proxy, to enabled states.
 * $this and self keyword in all methods of the stated class instance (in proxy's method and states' methods)
 * represent the proxy instance.
 *
 * The proxy class is mandatory. States 3.0 has no factories, no loader. Proxies embedded directly theirs states
 * configurations. Since 3.0, states's methods are a builder, returning a real closure to use. The state does not use
 * the Reflection API to extract the closure (not bindable with new $this since 7.1).
 *
 * This new architecture is more efficient and is simpler.
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
