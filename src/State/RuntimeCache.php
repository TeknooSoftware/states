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

namespace Teknoo\States\State;

use Closure;
use ReflectionClass;
use ReflectionMethod;

/**
 * Holder of all values computed at runtime by a state (reflections, closures returned by builders and results of
 * visibility's checks) to not compute them again at each call.
 *
 * Reflections and closures are not serializable. States are shared between all instances of a same stated class, so
 * a single call of a state's method forbade the serialization of all these instances. All these values can be
 * computed again : this holder is always serialized as an empty object to keep states, and theirs stated class
 * instances, serializable. It is a dedicated class to not add magic methods `__serialize()` and `__unserialize()`
 * to states : they are reserved to builders called by the `SerializableTrait` of the proxy.
 *
 * @internal
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
final class RuntimeCache
{
    /**
     * Reflection class object of the state to extract closures and description.
     *
     * @var ReflectionClass<covariant object>|null
     */
    public ?ReflectionClass $reflectionClass = null;

    /**
     * Reflections methods of the state to extract description and closures, false for methods not available.
     *
     * @var array<string, ReflectionMethod|false>
     */
    public array $reflectionsMethods = [];

    /**
     * List of closures already returned by builders.
     *
     * @var array<string, Closure>
     */
    public array $closuresObjects = [];

    /**
     * To know, for each closure returned by a builder, if it is static : a static closure can only be bound to the
     * scope of the stated class, never to its instance.
     *
     * @var array<string, bool>
     */
    public array $staticClosures = [];

    /**
     * Results of visibility's checks, by scope, by stated class origin and by method name.
     *
     * @var array<string, array<string, array<string, bool>>>
     */
    public array $visibilityCache = [];

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
    }
}
