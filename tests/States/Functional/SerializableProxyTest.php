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

namespace Teknoo\Tests\States\Functional;

use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Serializable\SerializableProxy;
use Teknoo\Tests\Support\Serializable\States\Other;

/**
 * With the SerializableTrait, the serialization of a stated class instance is delegated to the method
 * `__serialize()` provided by one of its enabled states : states must not provide themselves a magic method
 * `__serialize()`, else it is called by the proxy instead of the state's builder.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class SerializableProxyTest extends TestCase
{
    public function testSerializationIsDelegatedToTheEnabledStateProvidingIt(): void
    {
        $proxy = new SerializableProxy('foo');
        $proxy->enableState(Other::class);

        //States have been called : they keep reflections and closures
        $this->assertSame('FOO', $proxy->getUpperValue());
        $this->assertSame(['value' => 'foo'], $proxy->__serialize());

        $unserializedProxy = unserialize(serialize($proxy));
        $this->assertInstanceOf(SerializableProxy::class, $unserializedProxy);
        $this->assertSame('foo', $unserializedProxy->value);
    }
}
