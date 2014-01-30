<?php

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