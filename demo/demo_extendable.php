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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace demo;

use Acme\Extendable\Daughter\Daughter;
use Acme\Extendable\GrandDaughter\GrandDaughter;
use Acme\Extendable\Mother\Mother;
use UniAlteri\States\Exception\MethodNotImplemented;

$loader = include 'demo.php';

//Register demo namespace
$loader->registerNamespace('\\Acme\\Extendable', __DIR__.DS.'Acme'.DS.'Extendable');

print 'Uni Alteri - States library - Demo for extendable behavior:'.PHP_EOL;

//Initialize objects
$motherInstance = new Mother();
$daughterInstance = new Daughter();
$grandDaughterInstance = new GrandDaughter();

print PHP_EOL.'List states available for the mother class : '.GREEN_COLOR;
print implode(', ', $motherInstance->listAvailableStates());
print RESET_COLOR.PHP_EOL.'List states available for the daughter class, include mother\'s states and overloaded states : '.GREEN_COLOR;
print implode(', ', $daughterInstance->listAvailableStates());
print RESET_COLOR.PHP_EOL.'List states available for the grand daughter class, include parents\' states, overloaded and extended states : '.GREEN_COLOR;
print implode(', ', $grandDaughterInstance->listAvailableStates());

print RESET_COLOR.PHP_EOL.PHP_EOL.'List methods available for the mother class : '.GREEN_COLOR;
foreach ($motherInstance->listMethodsByStates() as $stateName => $methodsList) {
    print PHP_EOL."\t".$stateName.' : '.implode(', ', $methodsList);
}
print RESET_COLOR.PHP_EOL.'List methods available for the daughter class, include mother\'s states and overloaded states : '.GREEN_COLOR;
foreach ($daughterInstance->listMethodsByStates() as $stateName => $methodsList) {
    print PHP_EOL."\t".$stateName.' : '.implode(', ', $methodsList);
}
print RESET_COLOR.PHP_EOL.'List methods available for the grand daughter class, include parents\' states, overloaded and extended states : '.GREEN_COLOR;
foreach ($grandDaughterInstance->listMethodsByStates() as $stateName => $methodsList) {
    print PHP_EOL."\t".$stateName.' : '.implode(', ', $methodsList);
}
print RESET_COLOR.PHP_EOL;

$motherInstance->enableState('StateOne');
print PHP_EOL.'Call method1 of mother object : '.GREEN_COLOR.$motherInstance->method1().RESET_COLOR;
print PHP_EOL.'Call method2 of mother object : '.GREEN_COLOR.$motherInstance->method2().RESET_COLOR;

$daughterInstance->enableState('StateOne');
print PHP_EOL.'Call method3 of daughter object : '.GREEN_COLOR.$daughterInstance->method3().RESET_COLOR;
print PHP_EOL.'Call method4 of daughter object : '.GREEN_COLOR.$daughterInstance->method4().RESET_COLOR.PHP_EOL;
print PHP_EOL.'Forbid call of mother method 1 from daughter object (StateOne is overloaded in Daughter class and method1 has not been defined here) : ';
try {
    $daughterInstance->method1();
    print RED_COLOR.'Error, method called :/'.RESET_COLOR;
    print 'Demo failed';
    exit;
} catch (MethodNotImplemented $e) {
    print GREEN_COLOR.PHP_EOL.'OK, the method1 has not been defined in overloaded state, it\'s not available in the daughter class'.RESET_COLOR;
} catch (\Exception $e) {
    print PHP_EOL.RED_COLOR.' Error '.$e->getMessage().RESET_COLOR;
    print 'Demo failed';
    exit;
}

$grandDaughterInstance->enableState('StateThree');
print PHP_EOL.'Call daughter method6 from a granddaughter object with the StateThree extended, not overloaded : ';
print GREEN_COLOR.$grandDaughterInstance->method6().RESET_COLOR;
print PHP_EOL.'Call now the method7 defined in this state for GrandDaughter class : ';
print GREEN_COLOR.$grandDaughterInstance->method7().RESET_COLOR.PHP_EOL;

print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method, by a mother object : ';
$motherInstance->enableState('StateTwo');
print GREEN_COLOR.$motherInstance->methodRecallPrivate().RESET_COLOR;
print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method, by a daughter object : ';
$daughterInstance->enableState('StateTwo');
print GREEN_COLOR.$daughterInstance->methodRecallPrivate().RESET_COLOR;

print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method in daughter class, by a daughter object : ';
$daughterInstance->enableState('StateTwo')->enableState('StateThree');

try {
    $daughterInstance->methodRecallMotherPrivate();
    print RED_COLOR.'Error, method called :/'.RESET_COLOR;
    print 'Demo failed';
    exit;
} catch (MethodNotImplemented $e) {
    print GREEN_COLOR.PHP_EOL.'Ok, the method is not available directly by daughter object'.RESET_COLOR;
} catch (\Exception $e) {
    print PHP_EOL.RED_COLOR.'Error '.$e->getMessage().RESET_COLOR;
    print 'Demo failed';
    exit;
}

print PHP_EOL.PHP_EOL.GREEN_COLOR.'Demo finished'.RESET_COLOR.PHP_EOL;
