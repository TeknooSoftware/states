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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\States\States;

use \UniAlteri\States\Proxy;
use \UniAlteri\Tests\Support;

class StateTest extends AbstractStatesTest
{
    /**
     * Build an basic object to provide only public methods
     * @param boolean $initializeContainer initialize virtual di container for state
     * @return Support\OnlyPublic
     */
    protected function _getPublicClassObject($initializeContainer=true){
        return new Support\OnlyPublic($initializeContainer);
    }

    /**
     * Build an basic object to provide only protected methods
     * @param boolean $initializeContainer initialize virtual di container for state
     * @return Support\OnlyProtected
     */
    protected function _getProtectedClassObject($initializeContainer=true){
        return new Support\OnlyProtected($initializeContainer);
    }

    /**
     * Build an basic object to provide only private methods
     * @param boolean $initializeContainer initialize virtual di container for state
     * @return Support\OnlyPrivate
     */
    protected function _getPrivateClassObject($initializeContainer=true){
        return new Support\OnlyPrivate($initializeContainer);
    }

    /**
     * Build a virtual proxy for test
     * @return Proxy\ProxyInterface
     */
    protected function _getVirtualProxy(){
        return new Support\VirtualProxy(array());
    }
}