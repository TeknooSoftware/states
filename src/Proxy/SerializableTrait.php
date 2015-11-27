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
 * Trait SerializableTrait
 * Trait to use the interface \Serializable (http://php.net/manual/en/class.serializable.php) with stated classes
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @method mixed findMethodToCall($name, $args)
 */
trait SerializableTrait
{
    /*****************
     * Serialization *
     *****************/

    /**
     * To serialize the object.
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     *
     * @return string
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function serialize(): string
    {
        $args = [];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To wake up the object.
     * @api
     *
     * @param string $serialized
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function unserialize($serialized)
    {
        $args = [$serialized];
        $this->findMethodToCall(__FUNCTION__, $args);
    }
}
