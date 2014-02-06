<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 17/10/13
 * Time: 10:48
 */

namespace UniAlteri\Tests\States\Proxy;


class StandardTest extends AbstractProxyTest
{
    /**
     * Build a proxy object, into $this->_proxy to test it
     */
    protected function _buildProxy()
    {
        $this->_proxy = new Proxy\Standard();
    }
}