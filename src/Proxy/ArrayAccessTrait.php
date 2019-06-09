<?php

declare(strict_types=1);

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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Proxy;

/**
 * Trait ArrayAccessTrait
 * Trait to use the interface \ArrayAccess (http://php.net/manual/en/class.arrayaccess.php) with a stated classes.
 * It must be used with the trait ProxyTrait. This trait forwards all methods defined in \ArrayAccess in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @mixin \ArrayAccess
 * @mixin ProxyTrait
 */
trait ArrayAccessTrait
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function count(): int
    {
        $args = [];
        $methodName = __FUNCTION__;

        return (int) $this->findAndCall($methodName, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetExists($offset)
    {
        $args = [$offset];
        $methodName = __FUNCTION__;

        return $this->findAndCall($methodName, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetGet($offset)
    {
        $args = [$offset];
        $methodName = __FUNCTION__;

        return $this->findAndCall($methodName, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $value)
    {
        $args = [$offset, $value];
        $methodName = __FUNCTION__;

        return $this->findAndCall($methodName, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     * @throws \Throwable
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset): void
    {
        $args = [$offset];
        $methodName = __FUNCTION__;

        $this->findAndCall($methodName, $args);
    }
}
