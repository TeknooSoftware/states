<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

use ArrayAccess;
use Throwable;

/**
 * Trait to use the interface `\ArrayAccess` (http://php.net/manual/en/class.arrayaccess.php) with a stated classes.
 * It must be used with the trait `ProxyTrait`. This trait forwards all methods defined in `\ArrayAccess` in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @mixin ArrayAccess
 * @mixin ProxyTrait
 */
trait ArrayAccessTrait
{
    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function count(): int
    {
        $args = [];

        return (int) $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetExists(mixed $offset): bool
    {
        $args = [$offset];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetGet(mixed $offset): mixed
    {
        $args = [$offset];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $args = [$offset, $value];

        $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset(mixed $offset): void
    {
        $args = [$offset];

        $this->__call(__FUNCTION__, $args);
    }
}
