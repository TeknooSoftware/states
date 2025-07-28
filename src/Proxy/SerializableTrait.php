<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license* it is available in LICENSE file at the root of this package
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

namespace Teknoo\States\Proxy;

/**
 * Trait to use the interface \Serializable (https://www.php.net/manual/fr/language.oop5.magic.php#object.serialize)
 * with stated classes. It must be used with the trait ProxyTrait. This trait forwards __serialize method to
 * method defined in states of the class.
 *
 * @see https://www.php.net/manual/fr/language.oop5.magic.php#object.serialize
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 */
trait SerializableTrait
{
    /**
     * @api
     *
     * @return array<string, mixed>
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __serialize(): array
    {
        $args = [];

        return $this->__call(__FUNCTION__, $args);
    }
}
