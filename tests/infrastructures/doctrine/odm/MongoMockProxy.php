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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Doctrine\Document;

use Closure;
use Teknoo\Tests\Support\MockProxy;

/**
 * Class MongoMockProxy.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class MongoMockProxy extends MockProxy implements \Doctrine\ODM\MongoDB\Proxy\Proxy
{
    /**
     * Retrieves the callback to be used when cloning the proxy.
     *
     * @see __setCloner
     *
     * @return Closure|null
     */
    public function __getCloner()
    {
    }

    /**
     * Retrieves the initializer callback used to initialize the proxy.
     *
     * @see __setInitializer
     *
     * @return Closure|null
     */
    public function __getInitializer()
    {
    }

    /**
     * Retrieves the list of lazy loaded properties for a given proxy.
     *
     * @return array Keys are the property names, and values are the default values
     *               for those properties.
     */
    public function __getLazyProperties()
    {
    }

    /**
     * Returns whether this proxy is initialized or not.
     *
     * @return bool
     */
    public function __isInitialized()
    {
    }

    /**
     * Sets the callback to be used when cloning the proxy. That initializer should accept
     * a single parameter, which is the cloned proxy instance itself.
     *
     * @param Closure|null $cloner
     */
    public function __setCloner(Closure $cloner = null)
    {
    }

    /**
     * Marks the proxy as initialized or not.
     *
     * @param bool $initialized
     */
    public function __setInitialized($initialized)
    {
    }

    /**
     * Sets the initializer callback to be used when initializing the proxy. That
     * initializer should accept 3 parameters: $proxy, $method and $params. Those
     * are respectively the proxy object that is being initialized, the method name
     * that triggered initialization and the parameters passed to that method.
     *
     * @param Closure|null $initializer
     */
    public function __setInitializer(Closure $initializer = null)
    {
    }

    /**
     * Initializes this proxy if its not yet initialized.
     *
     * Acts as a no-op if already initialized.
     */
    public function __load()
    {
    }
}
