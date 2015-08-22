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
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace demo;

use Acme\Extendable\Daughter\Daughter;
use Acme\Extendable\GrandDaughter\GrandDaughter;
use Acme\Extendable\Mother\Mother;
use UniAlteri\States\Exception\MethodNotImplemented;

error_reporting(E_ALL | E_STRICT);

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Loading lib States
$loader = require_once dirname(__DIR__).DS.'src'.DS.'UniAlteri'.DS.'States'.DS.'bootstrap_composer.php';

//Register demo namespace
$loader->registerNamespace('\\Acme\\Extendable', __DIR__.DS.'Acme'.DS.'Extendable');

print 'Uni Alteri - States library - Demo for extendable behavior:'.PHP_EOL;

//Initialize objects
$motherInstance = new Mother();
$daughterInstance = new Daughter();
$grandDaughterInstance = new GrandDaughter();

print PHP_EOL.'List states available for the mother class :';
print_r($motherInstance->listAvailableStates());
print PHP_EOL.'List states available for the daughter class, include mother\'s states and overloaded states :';
print_r($daughterInstance->listAvailableStates());
print PHP_EOL.'List states available for the grand daughter class, include parents\' states, overloaded and extended states :';
print_r($grandDaughterInstance->listAvailableStates());

print PHP_EOL.PHP_EOL.'List methods available for the mother class :';
print_r($motherInstance->listMethodsByStates());
print PHP_EOL.'List methods available for the daughter class, include mother\'s states and overloaded states :';
print_r($daughterInstance->listMethodsByStates());
print PHP_EOL.'List methods available for the grand daughter class, include parents\' states, overloaded and extended states :';
print_r($grandDaughterInstance->listMethodsByStates());

$motherInstance->enableState('StateOne');
print PHP_EOL.'Call method1 of mother object : '.$motherInstance->method1();
print PHP_EOL.'Call method2 of mother object : '.$motherInstance->method2();

$daughterInstance->enableState('StateOne');
print PHP_EOL.'Call method3 of daughter object : '.$daughterInstance->method3();
print PHP_EOL.'Call method4 of daughter object : '.$daughterInstance->method4();
print PHP_EOL.'Forbid call of mother method 1 from daughter object (StateOne is overloaded in Daughter class and method1 has not been defined here) :';
try {
    $daughterInstance->method1();
    print 'Error, method called :/';
} catch (MethodNotImplemented $e) {
    print 'OK, the method1 has not been defined in overloaded state, it\'s not available in the daughter class';
} catch (\Exception $e) {
    print PHP_EOL.' Error '.$e->getMessage();
}

$grandDaughterInstance->enableState('StateThree');
print PHP_EOL.'Call daughter method6 from a granddaughter object with the StateThree extended, not overloaded : ';
print $grandDaughterInstance->method6();
print PHP_EOL.'Call now the method7 defined in this state for GrandDaughter class : ';
print $grandDaughterInstance->method7();

print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method, by a mother object :';
$motherInstance->enableState('StateTwo');
print $motherInstance->methodRecallPrivate();
print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method, by a daughter object :';
$daughterInstance->enableState('StateTwo');
print $daughterInstance->methodRecallPrivate();

print PHP_EOL.'Test behavior when we call a private method defined in mother class, via a public method in daughter class, by a daughter object : Exception must be threw :';
$daughterInstance->enableState('StateTwo')->enableState('StateThree');

try {
    $daughterInstance->methodRecallMotherPrivate();
    print 'Error, method called :/';
} catch (MethodNotImplemented $e) {
    print PHP_EOL.'Ok, the method is not available directly by daughter object';
} catch (\Exception $e) {
    print PHP_EOL.'Error '.$e->getMessage();
}
