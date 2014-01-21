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
 * to license@centurion-project.org so we can send you a copy immediately.
 *
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\States;

interface StateInterface{
    const INJECTION_CLOSURE_IDENTIFIER = 'injectionClosure';

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(\UniAlteri\States\DI\ContainerInterface $container);

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * Return an array of string listing all methods available in the state
     * @return string[]
     */
    public function listMethods();

    /**
     * Test if a method exist into the
     * @param string $methodName
     * @return boolean
     */
    public function testMethod($methodName);

    /**
     * Return the description of a method to configure the behavior of the proxy
     * @param string $methodName
     * @return \ReflectionMethod
     */
    public function getMethodDescription($methodName);

    /**
     * Return a closure of the required method to use in the proxy
     * @param string $methodName
     * @param \UniAlteri\States\Proxy\ProxyInterface $proxy
     * @return \UniAlteri\States\DI\InjectionClosureInterface
     */
    public function getClosure($methodName, \UniAlteri\States\Proxy\ProxyInterface $proxy);
}