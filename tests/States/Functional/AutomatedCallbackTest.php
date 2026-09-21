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
use ReflectionProperty;
use Teknoo\Tests\Support\AutomatedAcme\CallbackAutomatedAcme;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;
use WeakMap;

use function serialize;
use function unserialize;

/**
 * Functional tests about automated stated classes whose assertions are callbacks on methods of the instance.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class AutomatedCallbackTest extends TestCase
{
    public function testUpdateStatesFromCallbacksOnMethodsOfTheInstance(): void
    {
        $instance = new CallbackAutomatedAcme('bar');
        $this->assertSame([], $instance->listEnabledStates());

        $instance->updateStates();
        $this->assertSame([State1::class], $instance->listEnabledStates());

        $instance->setFoo('foo')->updateStates();
        $this->assertSame([State2::class], $instance->listEnabledStates());
    }

    /**
     * Two instances with same values and same states must be equal, even when theirs assertions are callbacks
     * bound to each instance.
     */
    public function testInstancesWithSameValuesAreEqualAfterUpdateStates(): void
    {
        $instance1 = new CallbackAutomatedAcme('bar');
        $instance1->updateStates();

        $instance2 = new CallbackAutomatedAcme('bar');
        $instance2->updateStates();

        $this->assertEquals($instance1, $instance2);
        $this->assertTrue($instance1 == $instance2);

        $this->assertFalse($instance1 == new CallbackAutomatedAcme('foo')->updateStates());
    }

    /**
     * Compiled assertions are kept out of instances, in a list shared by all instances of the stated class : this
     * list must be a WeakMap, to never retain instances (an entry is removed with its instance).
     * (The release of an instance is not checked here : it requires to force the garbage collector, which needs
     * several megabytes when the code coverage is enabled).
     */
    public function testCompiledAssertionsAreKeptInAWeakMap(): void
    {
        $instance = new CallbackAutomatedAcme('bar');
        $instance->updateStates();

        $compiledAssertions = new ReflectionProperty(CallbackAutomatedAcme::class, 'compiledAssertions')->getValue();

        $this->assertInstanceOf(WeakMap::class, $compiledAssertions);
        $this->assertTrue(isset($compiledAssertions[$instance]));
        $this->assertFalse(isset($compiledAssertions[clone $instance]));
    }

    /**
     * An automated instance stays serializable after the compilation of its assertions.
     */
    public function testInstanceIsSerializableAfterUpdateStates(): void
    {
        $instance = new CallbackAutomatedAcme('bar');
        $instance->updateStates();

        $unserializedInstance = unserialize(serialize($instance));

        $this->assertInstanceOf(CallbackAutomatedAcme::class, $unserializedInstance);
        $this->assertSame([State1::class], $unserializedInstance->listEnabledStates());

        $unserializedInstance->setFoo('foo')->updateStates();
        $this->assertSame([State2::class], $unserializedInstance->listEnabledStates());
    }

    /**
     * Assertions are compiled once by instance, and callbacks defined with a method's name are bound to this
     * instance : a clone must not reuse assertions of the original instance, else its states are computed from
     * values of the original instance.
     */
    public function testUpdateStatesOnACloneUsesTheCloneData(): void
    {
        $original = new CallbackAutomatedAcme('bar');
        $original->updateStates();
        $this->assertSame([State1::class], $original->listEnabledStates());

        $clone = clone $original;
        $clone->setFoo('foo')->updateStates();

        $this->assertSame([State2::class], $clone->listEnabledStates());
        $this->assertSame([State1::class], $original->listEnabledStates());

        //The original instance is not impacted by its clone
        $original->updateStates();
        $this->assertSame([State1::class], $original->listEnabledStates());
    }
}
