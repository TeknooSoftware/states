<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.1.1
 */

namespace UniAlteri\Tests\States\Functional;

use UniAlteri\States\Di;
use UniAlteri\States\Exception\MethodNotImplemented;
use UniAlteri\States\Factory\FactoryInterface;
use UniAlteri\States\Loader;
use UniAlteri\Tests\Support\Extendable\Daughter\Daughter;
use UniAlteri\Tests\Support\Extendable\GrandDaughter\GrandDaughter;
use UniAlteri\Tests\Support\Extendable\Mother\Mother;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ExtendableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Loader of the states library.
     *
     * @var \UniAlteri\States\Loader\LoaderInterface
     */
    protected $loader = null;

    /**
     * Factory registry
     * @var Di\Container
     */
    protected $factoryRegistery = null;

    /**
     * Load the library State and retrieve its default loader from its bootstrap.
     *
     * @return \UniAlteri\States\Loader\LoaderInterface
     */
    protected function getLoader()
    {
        if (null === $this->loader) {
            $this->loader = include 'UniAlteri'.DS.'States'.DS.'bootstrap.php';
        }

        //To share the Factory interface in all context for each test
        $diContainer = $this->loader->getDiContainer();
        if ($diContainer->testEntry(FactoryInterface::DI_FACTORY_REPOSITORY)) {
            $this->factoryRegistery = $diContainer->get(FactoryInterface::DI_FACTORY_REPOSITORY);
        } else {
            $diContainer->registerInstance(FactoryInterface::DI_FACTORY_REPOSITORY, $this->factoryRegistery);
        }

        return $this->loader;
    }

    protected function setUp()
    {
        //Register demo namespace
        $this->getLoader()->registerNamespace(
            '\\UniAlteri\\Tests\\Support\\Extendable',
            UA_STATES_TEST_PATH.DS.'Support'.DS.'Extendable'
        );

        parent::setUp();
    }

    /**
     * Unload the loader from SPL Autoload to not interfere with others tests.
     */
    protected function tearDown()
    {
        if ($this->loader instanceof Loader\LoaderInterface) {
            spl_autoload_unregister(
                array($this->loader, 'loadClass')
            );
        }

        parent::tearDown();
    }

    /**
     * Check if the list of states for the mother contains only its states and not states of its children classes
     */
    public function testListAvailablesStatesMother()
    {
        $motherInstance = new Mother();
        $statesList = $motherInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            ['StateDefault', 'StateOne', 'StateTwo'],
            $statesList
        );
    }

    /**
     * Check if the list of states for the daughter contains only its states and states of its mother.
     * States overloaded must not be duplicated
     */
    public function testListAvailablesStatesDaughter()
    {
        $daughterInstance = new Daughter();
        $statesList = $daughterInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            ['StateDefault', 'StateOne', 'StateThree', 'StateTwo'],
            $statesList
        );
    }

    /**
     * Check if the list of states for the grand dauther contains its states, and non overloaded parents'states
     */
    public function testListAvailableStatesGrandDaughter()
    {
        $grandDaughterInstance = new GrandDaughter();
        $statesList = $grandDaughterInstance->listAvailableStates();
        sort($statesList);
        $this->assertEquals(
            ['StateDefault', 'StateOne', 'StateTwo', 'StateThree'],
            $statesList
        );
    }

    /**
     * Check if methods in states are only methods availables from the mother
     */
    public function testListMethodsByStatesMother()
    {
        $motherInstance = new Mother();
        $this->assertEquals(
            [
                'StateDefault' => [],
                'StateOne' => ['method1', 'method2'],
                'StateTwo' => ['methodPublic', 'methodProtected', 'methodPrivate']
            ],
            $motherInstance->listMethodsByStates()
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods
     */
    public function testListMethodsByStatesDaughter()
    {
        $daughterInstance = new Daughter();
        $this->assertEquals(
            [
                'StateDefault' => [],
                'StateOne' => ['method3', 'method4'],
                'StateTwo' => ['methodPublic', 'methodProtected'],
                'StateThree' => ['method6']
            ],
            $daughterInstance->listMethodsByStates()
        );
    }

    /**
     * Check if methods in states are only public and protected methods availables from the mother
     * (of non overloaded states) and its owned methods. Check inheritance state from a state in parent classe
     */
    public function testListMethodsByStatesGrandDaughter()
    {
        $daughterInstance = new Daughter();
        $this->assertEquals(
            [
                'StateDefault' => [],
                'StateOne' => ['method3', 'method4'],
                'StateTwo' => ['methodPublic', 'methodProtected'],
                'StateThree' => ['method6', 'method7']
            ],
            $daughterInstance->listMethodsByStates()
        );
    }

    /**
     * Test behavior of overloaded states
     */
    public function testOverloadedState()
    {
        $motherInstance = new Mother();
        $motherInstance->enableState('StateOne');
        $this->assertEquals(123, $motherInstance->method1());
        $this->assertEquals(456, $motherInstance->method2());
        $daughterInstance = new Daughter();
        $daughterInstance->enableState('StateOne');
        $this->assertEquals(321, $daughterInstance->method3());
        $this->assertEquals(654, $daughterInstance->method4());
        try {
            $daughterInstance->method1();
        } catch (MethodNotImplemented $e) {
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $this->fail('Error, the daughter class overload the StateOne, Mother\'s methods must not be available');
    }

    /**
     * Test behavior of overloaded states
     */
    public function testExtendedState()
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState('StateOne');
        $this->assertEquals(666, $daughterInstance->method6());

        $grandDaughterInstace = new GrandDaughter();
        $grandDaughterInstace->enableState('StateOne');
        $this->assertEquals(666, $grandDaughterInstace->method6());
        $this->assertEquals(777, $grandDaughterInstace->method7());
    }

    /**
     * Test if the php behavior on private method is keeped with stated class
     */
    public function testMotherCanCallPrivate()
    {
        $motherInstance = new Mother();
        $motherInstance->enableState('StateTwo');
        $this->assertEquals(2*789, $motherInstance->methodRecallPrivate());
    }

    /**
     * Test if the php behavior on protected method in parent class
     * is keeped with an extending stated class (can call)
     */
    public function testDaughterCanCallMotherProtected()
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState('StateTwo')
            ->enableState('StateThree');
        $this->assertEquals(3*456, $daughterInstance->methodRecallMotherProtected());
    }

    /**
     * Test if the php behavior on private method in parent class
     * is keeped with an extending stated class (can not call)
     */
    public function testDaughterCanNotCallMotherPrivate()
    {
        $daughterInstance = new Daughter();
        $daughterInstance->enableState('StateTwo')
            ->enableState('StateThree');

        try {
            $daughterInstance->methodRecallMotherProtected();
        } catch (MethodNotImplemented $e) {
            //Good behavior
            return;
        } catch (\Exception $e) {
            $this->fail('Error privates methods must not be available for daughter methods');
        }
    }
}
