<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Serializable;

use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\States\Proxy\SerializableTrait;
use Teknoo\Tests\Support\Serializable\States\Other;
use Teknoo\Tests\Support\Serializable\States\StateDefault;

/**
 * Stated class using the SerializableTrait : its serialization is delegated to the method `__serialize()` provided
 * by one of its enabled states.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[StateClass(StateDefault::class)]
#[StateClass(Other::class)]
class SerializableProxy implements ProxyInterface
{
    use ProxyTrait;
    use SerializableTrait;

    public function __construct(
        public string $value = '',
    ) {
        $this->initializeStateProxy();
    }
}
