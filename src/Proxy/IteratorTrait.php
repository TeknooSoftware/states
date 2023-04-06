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
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

use Throwable;

/**
 * Trait to use the interface `\Iterator` (http://php.net/manual/en/class.iterator.php) with stated classes.
 * It must be used with the trait `ProxyTrait`. This trait forwards all methods defined in `\Iterator` in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/class.iterator.php
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @mixin ProxyTrait
 */
trait IteratorTrait
{
    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function current(): mixed
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function key(): mixed
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function next(): void
    {
        $args = [];

        $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function rewind(): void
    {
        $args = [];

        $this->__call(__FUNCTION__, $args);
    }

    /**
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function seek($position): void
    {
        $args = [$position];

        $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function valid(): bool
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws Throwable
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function getIterator(): iterable
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }
}
