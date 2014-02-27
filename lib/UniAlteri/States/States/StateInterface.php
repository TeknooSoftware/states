<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  States
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\States;

use \UniAlteri\States\DI;
use \UniAlteri\States\Proxy;

interface StateInterface
{
    /**
     * Identifier into DI Container to generate a new Injection Closure Container
     */
    const INJECTION_CLOSURE_SERVICE_IDENTIFIER = 'injectionClosureService';

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container);

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * Return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods();

    /**
     * Test if a method exist for this state
     * @param string $methodName
     * @return boolean
     */
    public function testMethod($methodName);

    /**
     * Return the description of a method to configure the behavior of the proxy
     * @param string $methodName
     * @return \ReflectionMethod
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getMethodDescription($methodName);

    /**
     * Return a closure of the required method to use in the proxy
     * @param string $methodName
     * @param Proxy\ProxyInterface $proxy
     * @return DI\InjectionClosureInterface
     * @throws Exception\MethodNotImplemented is the method does not exist
     */
    public function getClosure($methodName, Proxy\ProxyInterface $proxy);
}