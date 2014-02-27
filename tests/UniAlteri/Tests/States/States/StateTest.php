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
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\States\States;

use \UniAlteri\Tests\Support;

class StateTest extends AbstractStatesTest
{
    /**
     * Build an basic object to provide only public methods
     * @return Support\OnlyPublic
     */
    protected function _getPublicClassObject(){
        return new Support\OnlyPublic();
    }

    /**
     * Build an basic object to provide only protected methods
     * @return Support\OnlyProtected
     */
    protected function _getProtectedClassObject(){
        return new Support\OnlyProtected();
    }

    /**
     * Build an basic object to provide only private methods
     * @return Support\OnlyPrivate
     */
    protected function _getPrivateClassObject(){
        return new Support\OnlyPrivate();
    }

    /**
     * Build a virtual proxy for test
     * @return Proxy\ProxyInterface
     */
    protected function _getVirtualProxy(){
        return new Support\VirtualProxy();
    }
}