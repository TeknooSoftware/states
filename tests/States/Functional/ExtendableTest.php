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
use Teknoo\Tests\Support\Extendable\Mother\States\StateOne as StateOneMother;
use Teknoo\Tests\Support\Extendable\Mother\States\StateTwo;
use Teknoo\Tests\Support\Extendable\Daughter\States\StateDefault as StateDefaultDaughter;
use Teknoo\Tests\Support\Extendable\GrandDaughter\States\StateThree as StateThreeGrandDaughter;
use PHPUnit\Framework\TestCase;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ExtendableTest extends TestCase
{
    /**
     * @return Mother
     */
    public function buildMother()
    {
        return new Mother();
    }

    /**
     * @return Daughter
     */
    public function buildDaughter()
    {
        return new Daughter();
    }

    /**
     * @return GrandDaughter
     */
    public function buildGrandDaughter()
    {
        return new GrandDaughter();
    }

    /**
     * @return GrandGrandDaughter
     */
    public function buildGrandGrandDaughter()
    {
        return new GrandGrandDaughter();
    }

    /**
     * Check if the list of states for the mother contains only its states and not states of its children classes.
     */
    public function testListAvailablesStatesMother(): void
    {
        $motherInstance = $this->buildMother();
        $statesList = $motherInstance->listAvailableStates();
        sort($statesList);
        $this->assertSame([StateDefault::class, StateOneMother::class, StateTwo::class], $statesList);
    }

    /**
     * Check if the list of states for the daughter contains only its states and states of its mother.
     * States overloaded must not be duplicated.
     */
    public function testListAvailablesStatesDaughter(): void
    {
        $daughterInstance = $this->buildDaughter();
        $statesList = $daughterInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            [StateDefaultDaughter::class, StateOne::class, StateThree::class, StateTwo::class],
            $statesList
        );
    }

    /**
     * Check if the list of states for the grand dauther contains its states, and non overloaded parents'states.
     */
    public function testListAvailableStatesGrandDaughter(): void
    {
        $grandDaughterInstance = $this->buildGrandDaughter();
        $statesList = $grandDaughterInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            [
                StateDefaultDaughter::class,
                StateOne::class,
                StateFour::class,
                StateThreeGrandDaughter::class,
                StateTwo::class
            ],
            $statesList,
        );
    }

    /**
     * Check if the list of states for the grand dauther contains its states, and non overloaded parents'states.
     */
    public function testListAvailableStatesGrandGrandDaughter(): void
    {
        $grandGrandDaughterInstance = $this->buildGrandGrandDaughter();
        $statesList = $grandGrandDaughterInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            [
                StateDefaultDaughter::class,
                StateOne::class,
                StateFour::class,
                StateThreeGrandDaughter::class,
                StateTwo::class
            ],
            $statesList,
        );
    }

    /**
     * Check if methods in states are only methods availables from the mother.
     */
    public function testListMethodsByStatesMother(): void
    {
        $motherInstance = $this->buildMother();
        $this->assertEquals(
            [
                StateDefault::class => [],
                StateOneMother::class => [
                    'method1',
                    'method2',
                ],
                StateTwo::class => [
                    'methodPublic',
                    'methodProtected',
                    'methodPrivate',
                    'methodRecallPrivate',
                    'updateVariable',
                    'getMotherVariable',
                ],
            ],
            $motherInstance->listMethodsByStates(),
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods.
     */
    public function testListMethodsByStatesDaughter(): void
    {
        $daughterInstance = $this->buildDaughter();
        $this->assertEquals(
            [
                StateDefaultDaughter::class => [],
                StateOne::class => ['method3', 'method4'],
                StateThree::class => ['method6', 'methodRecallMotherPrivate', 'methodRecallMotherProtected'],
                StateTwo::class => [
                    'methodPublic',
                    'methodProtected',
                    'methodPrivate',
                    'methodRecallPrivate',
                    'updateVariable',
                    'getMotherVariable'
                ],
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
        $grandDaughterInstance = $this->buildGrandDaughter();
        $this->assertEquals(
            [
                StateDefaultDaughter::class => [],
                StateOne::class => ['method3', 'method4'],
                StateThreeGrandDaughter::class => [
                    'method7',
                    'method6',
                    'methodRecallMotherPrivate',
                    'methodRecallMotherProtected'
                ],
                StateTwo::class => [
                    'methodPublic',
                    'methodProtected',
                    'methodPrivate',
                    'methodRecallPrivate',
                    'updateVariable',
                    'getMotherVariable'
                ],
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
        $grandGrandDaughterInstance = $this->buildGrandGrandDaughter();
        $this->assertEquals(
            [
                StateDefaultDaughter::class => [],
                StateOne::class => ['method3', 'method4'],
                StateThreeGrandDaughter::class => [
                    'method7',
                    'method6',
                    'methodRecallMotherPrivate',
                    'methodRecallMotherProtected'
                ],
                StateTwo::class => [
                    'methodPublic',
                    'methodProtected',
                    'methodPrivate',
                    'methodRecallPrivate',
                    'updateVariable',
                    'getMotherVariable'
                ],
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
        $motherInstance = $this->buildMother();
        $motherInstance->enableState(StateOneMother::class);
        $this->assertEquals(123, $motherInstance->method1());
        $this->assertEquals(456, $motherInstance->method2());

        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateOne::class);
        $this->assertEquals(321, $daughterInstance->method3());
        $this->assertEquals(654, $daughterInstance->method4());

        $fail = false;
        try {
            $daughterInstance->method1();
        } catch (MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
            self::fail($e->getMessage());

            return;
        }

        $this->assertTrue($fail, 'Error, the method 3 are currently not available in enabled states');

        $daughterInstance->disableAllStates();
        $fail = false;
        try {
            $daughterInstance->method3();
        } catch (MethodNotImplemented $e) {
            $fail = true;
        } catch (\Exception $e) {
            self::fail($e->getMessage());
        }

        $this->assertTrue($fail, 'Error, the method 3 are currently not available in enabled states');

        $daughterInstance->enableState(StateOneMother::class);
        $this->assertEquals(321, $daughterInstance->method3());
        $this->assertEquals(654, $daughterInstance->method4());

        try {
            $daughterInstance->method1();
        } catch (MethodNotImplemented) {
            return;
        } catch (\Exception $e) {
            self::fail($e->getMessage());

            return;
        }

        self::fail("Error, the daughter class overload the StateOne, Mother's methods must not be available");
    }

    /**
     * Test behavior of overloaded states.
     */
    public function testExtendedState(): void
    {
        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateThree::class);
        $this->assertEquals(666, $daughterInstance->method6());

        $grandDaughterInstance = $this->buildGrandDaughter();
        $grandDaughterInstance->enableState(StateThreeGD::class);
        $this->assertEquals(666, $grandDaughterInstance->method6());
        $this->assertEquals(777, $grandDaughterInstance->method7());

        $grandGrandDaughterInstance = $this->buildGrandGrandDaughter();
        $grandGrandDaughterInstance->enableState(StateThreeGD::class);
        $this->assertEquals(666, $grandGrandDaughterInstance->method6());
        $this->assertEquals(777, $grandGrandDaughterInstance->method7());
    }

    /**
     * Test if the php behavior on private method is keeped with stated class.
     */
    public function testMotherCanCallPrivate(): void
    {
        $motherInstance = $this->buildMother();
        $motherInstance->enableState(StateTwo::class);
        $this->assertEquals(2 * 789, $motherInstance->methodRecallPrivate());
    }

    /**
     * Test if the php behavior on private method in parent class
     * is keeped with an extending stated class (can call if the method is in mother scope).
     */
    public function testDaughterCanCallPrivateViaMotherMethod(): void
    {
        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateTwo::class);
        $this->assertEquals(2 * 789, $daughterInstance->methodRecallPrivate());
    }

    /**
     * Test if the php behavior on protected method in parent class
     * is keeped with an extending stated class (can call).
     */
    public function testDaughterCanCallMotherProtected(): void
    {
        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateTwo::class)
            ->enableState(StateThree::class);
        $this->assertEquals(3 * 456, $daughterInstance->methodRecallMotherProtected());
    }

    /**
     * Test if the php behavior on private method in parent class
     * is keeped with an extending stated class (can not call).
     */
    public function testDaughterCanNotCallMotherPrivate(): void
    {
        $this->expectException(MethodNotImplemented::class);

        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateTwo::class)
            ->enableState(StateThree::class);

        $daughterInstance->methodRecallMotherPrivate();
    }

    public function testAccessToPrivateAttributeWithBindOfClosure(): void
    {
        $daughterInstance = $this->buildDaughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        $this->assertEquals('fooBar', $daughterInstance->classicGetMotherVariable());

        $this->assertEquals('fooBar', $daughterInstance->getMotherVariable());
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandDaughter(): void
    {
        $daughterInstance = $this->buildGrandDaughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        $this->assertEquals('fooBar', $daughterInstance->classicGetMotherVariable());

        $this->assertEquals('fooBar', $daughterInstance->getMotherVariable());
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandGrandDaughter(): void
    {
        $daughterInstance = $this->buildGrandGrandDaughter();
        $daughterInstance->enableState(StateTwo::class);

        $daughterInstance->updateVariable('fooBar');

        $this->assertEquals('fooBar', $daughterInstance->classicGetMotherVariable());

        $this->assertEquals('fooBar', $daughterInstance->getMotherVariable());
    }

    public function testStatesLoadingWhenStatesListDeclarationIsNotImplementedInClass(): void
    {
        $gdIinstance = $this->buildGrandDaughter();
        $gdIinstance->enableState(StateFour::class);
        $this->assertEquals(42, $gdIinstance->getPrivateValueOfGrandGauther());

        $gddInstance = $this->buildGrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);
        $this->assertEquals(42, $gddInstance->getPrivateValueOfGrandGauther());
    }

    public function testCallPrivateMethodFromAPublicMethodInParent(): void
    {
        $gddInstance = $this->buildGrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);

        $this->assertSame(42, $gddInstance->callPrivateMethod());
    }

    public function testCallPrivateMethodFromAPublicMethod(): void
    {
        $this->expectException(MethodNotImplemented::class);

        $gddInstance = $this->buildGrandGrandDaughter();
        $gddInstance->enableState(StateFour::class);

        $gddInstance->callPrivateMethodSo();
    }
}
