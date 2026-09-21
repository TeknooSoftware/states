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

namespace Teknoo\Tests\States\States;

use My\Stated\ClassName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\AbstractState;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;
use Teknoo\States\State\Visibility;
use Teknoo\Tests\Support;
use Teknoo\Tests\Support\MockAbstractOnlyPrivate;
use Teknoo\Tests\Support\MockOnlyPrivate;
use Teknoo\Tests\Support\MockOnlyProtected;
use Teknoo\Tests\Support\MockOnlyPublic;

/**
 * Class StateTest
 * Implementation of AbstractStatesTests to test the trait \Teknoo\States\State\StateTrait and
 * the abstract class \Teknoo\States\State\AbstractState.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversTrait(StateTrait::class)]
#[CoversClass(AbstractState::class)]
class StateTest extends AbstractStatesTests
{
    /**
     * Build a basic object to provide only public methods.
     *
     */
    protected function getPublicClassObject(
        bool $privateMode,
        string $statedClassName,
        array $aliases = []
    ): MockOnlyPublic {
        return new MockOnlyPublic($privateMode, $statedClassName, $aliases);
    }

    /**
     * Build a basic object to provide only protected methods.
     *
     */
    protected function getProtectedClassObject(
        bool $privateMode,
        string $statedClassName,
        array $aliases = []
    ): MockOnlyProtected {
        return new MockOnlyProtected($privateMode, $statedClassName, $aliases);
    }

    /**
     * Build a basic object to provide only private methods.
     *
     */
    protected function getPrivateClassObject(
        bool $privateMode,
        string $statedClassName,
        array $aliases = []
    ): MockOnlyPrivate {
        return new MockOnlyPrivate($privateMode, $statedClassName, $aliases);
    }

    /**
     * Private methods of a state extending the AbstractState class are not visible from the scope of this abstract
     * class, where the StateTrait is used : they must be callable like private methods of states using directly
     * the StateTrait.
     */
    #[DataProvider('privateModesProvider')]
    public function testExecutePrivateMethodOfAStateExtendingAbstractState(bool $privateMode): void
    {
        $called = false;
        $args = [
            $this->createStub(ProxyInterface::class),
            'standardMethod10',
            [1, 2],
            Visibility::Private,
            ClassName::class,
            function ($result) use (&$called): void {
                $this->assertEquals(3, $result);
                $called = true;
            }
        ];

        $this->assertInstanceOf(
            StateInterface::class,
            new MockAbstractOnlyPrivate($privateMode, ClassName::class)->executeClosure(...$args)
        );

        $this->assertTrue($called, 'Error, the private method standardMethod10 has not been called');
    }

    /**
     * A state keeps reflections and closures to not rebuild them at each call : they are not serializable, but they
     * can be rebuilt, so they must not forbid the serialization of the state (and so of its stated class instance).
     */
    public function testStateIsSerializableAfterExecuteClosure(): void
    {
        $result = null;
        $buildArgs = function () use (&$result): array {
            return [
                $this->createStub(ProxyInterface::class),
                'standardMethod1',
                [1, 2],
                Visibility::Public,
                ClassName::class,
                function ($value) use (&$result): void {
                    $result = $value;
                }
            ];
        };

        $state = new MockOnlyPublic(false, ClassName::class);
        $state->executeClosure(...$buildArgs());
        $this->assertSame(3, $result);

        $unserializedState = unserialize(serialize($state));
        $this->assertInstanceOf(MockOnlyPublic::class, $unserializedState);

        $result = null;
        $unserializedState->executeClosure(...$buildArgs());
        $this->assertSame(3, $result);

        //The state was called, it must stay cloneable and serializable
        $this->assertInstanceOf(MockOnlyPublic::class, unserialize(serialize(clone $unserializedState)));
    }

    /**
     * Closures and results of visibility's checks are kept by the state to be reused : once the closure of a private
     * method is built for an allowed caller, a caller not allowed must always be denied, at each call and whatever
     * the order of calls.
     */
    public function testDeniedCallerStaysDeniedOnceTheClosureIsBuilt(): void
    {
        $state = new MockOnlyPrivate(false, ClassName::class);

        $calls = 0;
        $buildArgs = function (Visibility $scope) use (&$calls): array {
            return [
                $this->createStub(ProxyInterface::class),
                'standardMethod10',
                [1, 2],
                $scope,
                ClassName::class,
                function ($result) use (&$calls): void {
                    $this->assertSame(3, $result);
                    ++$calls;
                }
            ];
        };

        //Allowed caller : the closure is built and kept
        $state->executeClosure(...$buildArgs(Visibility::Private));
        $state->executeClosure(...$buildArgs(Visibility::Private));
        $this->assertSame(2, $calls);

        //Callers not allowed : always denied, at the first call and at next calls
        $state->executeClosure(...$buildArgs(Visibility::Public));
        $state->executeClosure(...$buildArgs(Visibility::Public));
        $state->executeClosure(...$buildArgs(Visibility::Protected));
        $state->executeClosure(...$buildArgs(Visibility::Protected));
        $this->assertSame(2, $calls);

        //The allowed caller is always allowed
        $state->executeClosure(...$buildArgs(Visibility::Private));
        $this->assertSame(3, $calls);
    }

    /**
     * The builder of a method must not be executed when the caller is not allowed to call this method : the
     * visibility must be checked before.
     */
    public function testBuilderIsNotCalledWhenVisibilityIsDenied(): void
    {
        MockOnlyPrivate::$builderCalls = 0;
        $state = new MockOnlyPrivate(false, ClassName::class);

        $called = false;
        $buildArgs = function (Visibility $scope) use (&$called): array {
            return [
                $this->createStub(ProxyInterface::class),
                'methodWithBuilderSideEffect',
                [],
                $scope,
                ClassName::class,
                function ($result) use (&$called): void {
                    $this->assertSame('foo', $result);
                    $called = true;
                }
            ];
        };

        //Private method called from a public scope, then from a protected scope : denied
        $state->executeClosure(...$buildArgs(Visibility::Public));
        $state->executeClosure(...$buildArgs(Visibility::Protected));
        $this->assertFalse($called);
        $this->assertSame(0, MockOnlyPrivate::$builderCalls, 'The builder was executed for a denied caller');

        //Allowed from a private scope, the builder is executed only once
        $state->executeClosure(...$buildArgs(Visibility::Private));
        $this->assertTrue($called);
        $state->executeClosure(...$buildArgs(Visibility::Private));
        $this->assertSame(1, MockOnlyPrivate::$builderCalls);
    }
}
