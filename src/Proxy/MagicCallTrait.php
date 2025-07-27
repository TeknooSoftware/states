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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Proxy;

use Exception;
use Teknoo\States\Proxy\Exception\AvailableSeveralMethodImplementations;
use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Throwable;

/**
 * Trait to use PHP magic's calls (http://php.net/manual/en/language.oop5.magic.php) with stated classes.
 * It must be used with the trait `ProxyTrait`. This trait forwards call to __invoke() and __toString in methods
 * defined in states of the class.
 *
 * @see http://php.net/manual/en/language.oop5.magic.php
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @mixin ProxyTrait
 */
trait MagicCallTrait
{
    /**
     * To invoke an object as a function.
     * Warning : This method forwards the call the state's methode "invoke" and not "__invoke".
     *
     * @param array<mixed> $args
     * @throws Exception
     * @throws Throwable
     * @api
     *
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
     * @throws AvailableSeveralMethodImplementations
     * @throws MethodNotImplemented
     *
     * @api
     *
     *  @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
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
