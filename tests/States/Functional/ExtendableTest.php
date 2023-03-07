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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Functional;

use Teknoo\States\Proxy\Exception\MethodNotImplemented;
use Teknoo\Tests\Support\Extendable\Daughter\Daughter;
use Teknoo\Tests\Support\Extendable\Daughter\States\StateOne;
use Teknoo\Tests\Support\Extendable\Daughter\States\StateThree;
use Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateFour;
use Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree as StateThreeGD;
use Teknoo\Tests\Support\Extendable\GrandDaughter\GrandDaughter;
use Teknoo\Tests\Support\Extendable\GrandGrandDaughter\GrandGrandDaughter;
use Teknoo\Tests\Support\Extendable\Mother\Mother;
use Teknoo\Tests\Support\Extendable\Mother\States\StateDefault;
use Teknoo\Tests\Support\Extendable\Mother\States\StateTwo;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ExtendableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check if the list of states for the mother contains only its states and not states of its children classes.
     */
    public function testListAvailablesStatesMother(): void
    {
        $motherInstance = new Mother();
        $statesList = $motherInstance->listAvailableStates();
        sort($statesList);
        self::assertEquals(
            [StateDefault::class, \Teknoo\Tests\Support\Extendable\Mother\States\StateOne::class, StateTwo::class],
            $statesList
        );
    }

    /**
     * Check if the list of states for the daughter contains only its states and states of its mother.
     * States overloaded must not be duplicated.
     */
    public function testListAvailablesStatesDaughter(): void
    {
        $daughterInstance = new Daughter();
        $statesList = $daughterInstance->listAvailableStates();
        sort($statesList);
        self::assertEquals(
            [\Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class, StateOne::class, StateThree::class, StateTwo::class],
            $statesList
        );
    }

    /**
     * Check if the list of states for the grand dauther contains its states, and non overloaded parents'states.
     */
    public function testListAvailableStatesGrandDaughter(): void
    {
        $grandDaughterInstance = new GrandDaughter();
        $statesList = $grandDaughterInstance->listAvailableStates();
        sort($statesList);
        self::assertEquals(
            [
                \Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class,
                 StateOne::class,
                 StateFour::class,
                 \Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree::class,
                 StateTwo::class
            ],
            $statesList
        );
    }

    /**
     * Check if the list of states for the grand dauther contains its states, and non overloaded parents'states.
     */
    public function testListAvailableStatesGrandGrandDaughter(): void
    {
        $grandGrandDaughterInstance = new GrandGrandDaughter();
        $statesList = $grandGrandDaughterInstance->listAvailableStates();
        sort($statesList);
        self::assertEquals(
            [
                \Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class,
                StateOne::class,
                StateFour::class,
                \Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree::class,
                StateTwo::class
            ],
            $statesList
        );
    }

    /**
     * Check if methods in states are only methods availables from the mother.
     */
    public function testListMethodsByStatesMother(): void
    {
        $motherInstance = new Mother();
        self::assertEquals(
            [
                StateDefault::class => [],
                \Teknoo\Tests\Support\Extendable\Mother\States\StateOne::class => ['method1', 'method2'],
                StateTwo::class => ['methodPublic', 'methodProtected', 'methodPrivate', 'methodRecallPrivate', 'updateVariable', 'getMotherVariable'],
            ],
            $motherInstance->listMethodsByStates()
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods.
     */
    public function testListMethodsByStatesDaughter(): void
    {
        $daughterInstance = new Daughter();
        self::assertEquals(
            [
                \Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class => [],
                StateOne::class => ['method3', 'method4'],
                StateThree::class => ['method6', 'methodRecallMotherPrivate', 'methodRecallMotherProtected'],
                StateTwo::class => ['methodPublic', 'methodProtected', 'methodPrivate', 'methodRecallPrivate','updateVariable', 'getMotherVariable'],
            ],
            $daughterInstance->listMethodsByStates()
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods. Check inheritance state from a state in parent classe.
     */
    public function testListMethodsByStatesGrandDaughter(): void
    {
        $grandDaughterInstance = new GrandDaughter();
        self::assertEquals(
            [
                \Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class => [],
                StateOne::class => ['method3', 'method4'],
                \Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree::class => ['method7', 'method6', 'methodRecallMotherPrivate', 'methodRecallMotherProtected'],
                StateTwo::class => ['methodPublic', 'methodProtected', 'methodPrivate', 'methodRecallPrivate','updateVariable', 'getMotherVariable'],
                StateFour::class => ['getPrivateValueOfGrandGauther', 'thePrivateMethod'],
            ],
            $grandDaughterInstance->listMethodsByStates()
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods. Check inheritance state from a state in parent classe.
     */
    public function testListMethodsByStatesGrandGrandDaughter(): void
    {
        $grandGrandDaughterInstance = new GrandGrandDaughter();
        self::assertEquals(
            [
                \Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault::class => [],
                StateOne::class => ['method3', 'method4'],
                \Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree::class => ['method7', 'method6', 'methodRecallMotherPrivate', 'methodRecallMotherProtected'],
                StateTwo::class => ['methodPublic', 'methodProtected', 'methodPrivate', 'methodRecallPrivate','updateVariable', 'getMotherVariable'],
                StateFour::class => ['getPrivateValueOfGrandGauther', 'thePrivateMethod'],
            ],
            $grandGrandDaughterInstance->listMethodsByStates()
        );
    }

    /**
     * Test behavior of overloaded states.
     */
    public function testOverloadedState(): void
    {
        $motherInstance = new Mother();
        $motherInstance->enableState(\Teknoo\Tests\Support\Extendable\Mother\States\StateOne::class);
        self::assertEquals(123, $motherInstance->method1());
        self::assertEquals(456, $motherInstance->method2());

        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateOne::class);
        self::assertEquals(321, $daughterInstance->method3());
        self::assertEquals(654, $daughterInstance->method4());

        $fail = false;
        try {
            $daughterInstance->method1();
        } catch (MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
            self::fail($e->getMessage());

            return;
        }

        self::assertTrue($fail, 'Error, the method 3 are currently not available in enabled states');

        $daughterInstance->disableAllStates();
        $fail = false;
        try {
            $daughterInstance->method3();
        } catch (MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
            self::fail($e->getMessage());
        }

        self::assertTrue($fail, 'Error, the method 3 are currently not available in enabled states');

        $daughterInstance->enableState(\Teknoo\Tests\Support\Extendable\Mother\States\StateOne::class);
        self::assertEquals(321, $daughterInstance->method3());
        self::assertEquals(654, $daughterInstance->method4());

        try {
            $daughterInstance->method1();
        } catch (MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
            self::fail($e->getMessage());

            return;
        }

        self::fail('Error, the daughter class overload the StateOne, Mother\'s methods must not be available');
    }

    /**
     * Test behavior of overloaded states.
     */
    public function testExtendedState(): void
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateThree::class);
        self::assertEquals(666, $daughterInstance->method6());

        $grandDaughterInstance = new GrandDaughter();
        $grandDaughterInstance->enableState(StateThreeGD::class);
        self::assertEquals(666, $grandDaughterInstance->method6());
        self::assertEquals(777, $grandDaughterInstance->method7());

        $grandGrandDaughterInstance = new GrandGrandDaughter();
        $grandGrandDaughterInstance->enableState(StateThreeGD::class);
        self::assertEquals(666, $grandGrandDaughterInstance->method6());
        self::assertEquals(777, $grandGrandDaughterInstance->method7());
    }

    /**
     * Test if the php behavior on private method is keeped with stated class.
     */
    public function testMotherCanCallPrivate(): void
    {
        $motherInstance = new Mother();
        $motherInstance->enableState(StateTwo::class);
        self::assertEquals(2 * 789, $motherInstance->methodRecallPrivate());
    }

    /**
     * Test if the php behavior on private method in parent class
     * is keeped with an extending stated class (can call if the method is in mother scope).
     */
    public function testDaughterCanCallPrivateViaMotherMethod(): void
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateTwo::class);
        self::assertEquals(2 * 789, $daughterInstance->methodRecallPrivate());
    }

    /**
     * Test if the php behavior on protected method in parent class
     * is keeped with an extending stated class (can call).
     */
    public function testDaughterCanCallMotherProtected(): void
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateTwo::class)
            ->enableState(StateThree::class);
        self::assertEquals(3 * 456, $daughterInstance->methodRecallMotherProtected());
    }

    /**
     * Test if the php behavior on private method in parent class
     * is keeped with an extending stated class (can not call).
     */
    public function testDaughterCanNotCallMotherPrivate(): void
    {
        $this->expectException(\Teknoo\States\Proxy\Exception\MethodNotImplemented::class);

        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateTwo::class)
            ->enableState(StateThree::class);

        $daughterInstance->methodRecallMotherPrivate();
    }

    public function testAccessToPrivateAttributeWithBindOfClosure(): void
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        self::assertEquals(
            'fooBar',
            $daughterInstance->classicGetMotherVariable()
        );

        self::assertEquals(
            'fooBar',
            $daughterInstance->getMotherVariable()
        );
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandDaughter(): void
    {
        $daughterInstance = new GrandDaughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        self::assertEquals(
            'fooBar',
            $daughterInstance->classicGetMotherVariable()
        );

        self::assertEquals(
            'fooBar',
            $daughterInstance->getMotherVariable()
        );
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandGrandDaughter(): void
    {
        $daughterInstance = new GrandGrandDaughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        self::assertEquals(
            'fooBar',
            $daughterInstance->classicGetMotherVariable()
        );

        self::assertEquals(
            'fooBar',
            $daughterInstance->getMotherVariable()
        );
    }

    public function testStatesLoadingWhenStatesListDeclarationIsNotImplementedInClass(): void
    {
        $gdIinstance = new GrandDaughter();
        $gdIinstance->enableState(StateFour::class);
        self::assertEquals(42, $gdIinstance->getPrivateValueOfGrandGauther());

        $gddInstance = new GrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);
        self::assertEquals(42, $gddInstance->getPrivateValueOfGrandGauther());
    }

    public function testCallPrivateMethodFromAPublicMethodInParent(): void
    {
        $gddInstance = new GrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);

        self::assertEquals(
            42,
            $gddInstance->callPrivateMethod()
        );
    }

    public function testCallPrivateMethodFromAPublicMethod(): void
    {
        $this->expectException(MethodNotImplemented::class);

        $gddInstance = new GrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);

        $gddInstance->callPrivateMethodSo();
    }
}
