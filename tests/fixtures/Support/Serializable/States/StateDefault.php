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

namespace Teknoo\Tests\Support\Serializable\States;

use Closure;
use Teknoo\States\State\AbstractState;
use Teknoo\Tests\Support\Serializable\SerializableProxy;

/**
 * Default state, providing the method `__serialize()` called by the SerializableTrait of the proxy.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin SerializableProxy
 */
class StateDefault extends AbstractState
{
    /**
     * PHP requires the return type `array` when a return type is declared for this magic method : the builder must
     * be declared without return type.
     *
     * @return Closure
     */
    public function __serialize()
    {
        return fn (): array => ['value' => $this->value];
    }
}
