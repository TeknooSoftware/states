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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Proxy;

/**
 * Trait ArrayAccessTrait
 * Trait to use the interface \ArrayAccess with a stated classes
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait ArrayAccessTrait
{
    /****************
     * Array Access *
     ****************/

    /**
     * This method is executed when using the count() function on an object implementing Countable.
     * @api
     *
     * @return int
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function count(): int
    {
        $args = [];

        return (int) $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty() on states implementing ArrayAccess.
     * @api
     *
     * @param string|int $offset
     *
     * @return bool
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetExists($offset)
    {
        $args = [$offset];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Returns the value at specified offset.
     * This method is executed when checking if offset is empty().
     * @api
     *
     * @param string|int $offset
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetGet($offset)
    {
        $args = [$offset];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Assigns a value to the specified offset.
     * @api
     *
     * @param string|int $offset
     * @param mixed      $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetSet($offset, $value)
    {
        $args = [$offset, $value];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * Unset an offset.
     * @api
     *
     * @param string|int $offset
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function offsetUnset($offset)
    {
        $args = [$offset];
        $this->findMethodToCall(__FUNCTION__, $args);
    }
}
