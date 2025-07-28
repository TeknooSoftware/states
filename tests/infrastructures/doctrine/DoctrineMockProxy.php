<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Doctrine;

use Closure;
use ProxyManager\Proxy\LazyLoadingInterface;
use Teknoo\Tests\Support\MockProxy;

/**
 * Class DoctrineMockProxy.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class DoctrineMockProxy extends MockProxy implements LazyLoadingInterface
{
    /**
     * Retrieves the callback to be used when cloning the proxy.
     *
     * @see __setCloner
     */
    public function __getCloner(): void
    {
    }

    /**
     * Retrieves the initializer callback used to initialize the proxy.
     *
     * @see __setInitializer
     */
    public function __getInitializer(): void
    {
    }

    /**
     * Retrieves the list of lazy loaded properties for a given proxy.
     *
     * @return array Keys are the property names, and values are the default values
     *               for those properties.
     */
    public function __getLazyProperties(): void
    {
    }

    /**
     * Returns whether this proxy is initialized or not.
     */
    public function __isInitialized(): void
    {
    }

    /**
     * Sets the callback to be used when cloning the proxy. That initializer should accept
     * a single parameter, which is the cloned proxy instance itself.
     */
    public function __setCloner(?Closure $cloner = null): void
    {
    }

    /**
     * Marks the proxy as initialized or not.
     *
     * @param bool $initialized
     */
    public function __setInitialized($initialized): void
    {
    }

    /**
     * Sets the initializer callback to be used when initializing the proxy. That
     * initializer should accept 3 parameters: $proxy, $method and $params. Those
     * are respectively the proxy object that is being initialized, the method name
     * that triggered initialization and the parameters passed to that method.
     */
    public function __setInitializer(?Closure $initializer = null): void
    {
    }

    /**
     * Initializes this proxy if its not yet initialized.
     *
     * Acts as a no-op if already initialized.
     */
    public function __load(): void
    {
    }

    public function setProxyInitializer(?Closure $initializer = null): void
    {
        // TODO: Implement setProxyInitializer() method.
    }

    public function getProxyInitializer(): ?Closure
    {
        // TODO: Implement getProxyInitializer() method.
    }

    public function initializeProxy(): bool
    {
        // TODO: Implement initializeProxy() method.
    }

    public function isProxyInitialized(): bool
    {
        return false;
    }
}
