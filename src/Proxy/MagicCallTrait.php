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

use Exception;
use Throwable;

/**
 * Trait to use PHP magic's calls (http://php.net/manual/en/language.oop5.magic.php) with stated classes.
 * It must be used with the trait `ProxyTrait`. This trait forwards call to __invoke() and __toString in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/language.oop5.magic.php
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @mixin ProxyTrait
 */
trait MagicCallTrait
{
    /**
     * To invoke an object as a function.
     * Warning : This method forwards the call the state's methode "invoke" and not "__invoke".
     *
     * @api
     *
     * @throws Exception
     * @throws Throwable
     * @see http://php.net/manual/en/language.oop5.magic.php#object.invoke
     */
    public function __invoke(...$args): mixed
    {
        return $this->__call('invoke', $args);
    }

    /**
     * To transform the object to a string
     * You cannot throw an exception from within a __toString() method. Doing so will result in a fatal error.
     * Warning : This method forwards the call the state's methode "toString" and not '__toString".
     *
     * @api
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     */
    public function __toString(): string
    {
        try {
            $args = [];

            return (string) $this->__call('toString', $args);
        } catch (Throwable) {
            return '';
        }
    }
}
