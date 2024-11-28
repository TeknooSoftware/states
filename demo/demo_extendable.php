<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

namespace demo;

use Acme\Extendable\Daughter\Daughter;
use Acme\Extendable\GrandDaughter\GrandDaughter;
use Acme\Extendable\GrandDaughter\States\StateThree;
use Acme\Extendable\Mother\Mother;
use Acme\Extendable\Mother\States\StateOne;
use Acme\Extendable\Mother\States\StateTwo;
use Exception;
use Teknoo\States\Exception\MethodNotImplemented;

$composer = include __DIR__ . '/demo.php';
$composer->setPsr4('Acme\\', __DIR__.DS.'Acme'.DS);

echo 'Teknoo Software - States library - Demo for extendable behavior:'.PHP_EOL;

//Initialize objects
$motherInstance = new Mother();
$daughterInstance = new Daughter();
$grandDaughterInstance = new GrandDaughter();

echo PHP_EOL.'List states available for the mother class : '.GREEN_COLOR;
echo implode(', ', $motherInstance->listAvailableStates());
echo RESET_COLOR.PHP_EOL.'List states available for the daughter class, '
    . 'include mother\'s states and overloaded states : '.GREEN_COLOR;
echo implode(', ', $daughterInstance->listAvailableStates());
echo RESET_COLOR.PHP_EOL.'List states available for the grand daughter class, '
    . 'include parents\' states, overloaded and extended states : '.GREEN_COLOR;
echo implode(', ', $grandDaughterInstance->listAvailableStates());

$motherInstance->enableState(StateOne::class);
echo PHP_EOL.'Call method1 of mother object : '.GREEN_COLOR.$motherInstance->method1().RESET_COLOR;
echo PHP_EOL.'Call method2 of mother object : '.GREEN_COLOR.$motherInstance->method2().RESET_COLOR;

$daughterInstance->enableState(\Acme\Extendable\Daughter\States\StateOne::class);
echo PHP_EOL.'Call method3 of daughter object : '.GREEN_COLOR.$daughterInstance->method3().RESET_COLOR;
echo PHP_EOL.'Call method4 of daughter object : '.GREEN_COLOR.$daughterInstance->method4().RESET_COLOR.PHP_EOL;
$daughterInstance->disableAllStates();
$daughterInstance->enableState(StateOne::class);
echo PHP_EOL.'Call method3 of daughter object from mother '
    . 'state\'s name : '.GREEN_COLOR.$daughterInstance->method3().RESET_COLOR;
echo PHP_EOL.'Call method4 of daughter object from mother '
    . 'state\'s name : '.GREEN_COLOR.$daughterInstance->method4().RESET_COLOR.PHP_EOL;

echo PHP_EOL.'Forbid call of mother method 1 from daughter '
    . 'object (StateOne is overloaded in Daughter class and method1 has not been defined here) : ';
try {
    $daughterInstance->method1();
    echo RED_COLOR.'Error, method called :/'.RESET_COLOR;
    echo 'Demo failed';
    exit;
} catch (MethodNotImplemented) {
    echo GREEN_COLOR.PHP_EOL.'OK, the method1 has not been defined in overloaded state, '
        . 'it\'s not available in the daughter class'.RESET_COLOR;
} catch (Exception $e) {
    echo PHP_EOL.RED_COLOR.' Error '.$e->getMessage().RESET_COLOR;
    echo 'Demo failed';
    exit;
}

$grandDaughterInstance->enableState(StateThree::class);
echo PHP_EOL.'Call daughter method6 from a granddaughter object with the StateThree extended, not overloaded : ';
echo GREEN_COLOR.$grandDaughterInstance->method6().RESET_COLOR;
echo PHP_EOL.'Call now the method7 defined in this state for GrandDaughter class : ';
echo GREEN_COLOR.$grandDaughterInstance->method7().RESET_COLOR.PHP_EOL;

echo PHP_EOL.'Test behavior when we call a private method defined '
    . 'in mother class, via a public method, by a mother object : ';
$motherInstance->enableState(StateTwo::class);
echo GREEN_COLOR.$motherInstance->methodRecallPrivate().RESET_COLOR;
echo PHP_EOL.'Test behavior when we call a private method defined '
    . 'in mother class, via a public method, by a daughter object : ';
$daughterInstance->enableState(StateTwo::class);
echo GREEN_COLOR.$daughterInstance->methodRecallPrivate().RESET_COLOR;

echo PHP_EOL.'Test behavior when we call a private method defined '
    . 'in mother class, via a public method in daughter class, by a daughter object : ';
$daughterInstance->enableState(StateTwo::class)->enableState(\Acme\Extendable\Daughter\States\StateThree::class);

try {
    $daughterInstance->methodRecallMotherPrivate();
    echo RED_COLOR.'Error, method called :/'.RESET_COLOR;
    echo 'Demo failed';
    exit;
} catch (MethodNotImplemented) {
    echo GREEN_COLOR.PHP_EOL.'Ok, the method is not available directly by daughter object'.RESET_COLOR;
} catch (Exception $e) {
    echo PHP_EOL.RED_COLOR.'Error '.$e->getMessage().RESET_COLOR;
    echo 'Demo failed';
    exit;
}

echo PHP_EOL.PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
