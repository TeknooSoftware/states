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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

/**
 * Trait to use the interface \Serializable (http://php.net/manual/en/class.serializable.php) with stated classes.
 * It must be used with the trait ProxyTrait. This trait forwards all methods defined in \Serializable in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/class.serializable.php
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 */
trait SerializableTrait
{
    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     *
     * @see http://php.net/manual/en/class.serializable.php
     */
    public function serialize(): string
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }

    /**
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     *
     * @see http://php.net/manual/en/class.serializable.php
     */
    public function unserialize(string $serialized): mixed
    {
        $args = [$serialized];

        return $this->__call(__FUNCTION__, $args);
    }
}
