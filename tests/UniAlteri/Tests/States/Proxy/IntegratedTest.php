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

namespace UniAlteri\Tests\States\Proxy;

use PHPUnit_Framework_TestCase;
use \UniAlteri\States\Proxy;
use \UniAlteri\Tests\Support;

class IntegratedTest extends AbstractProxyTest
{
    protected function setUp()
    {
        include_once('UniAlteri/Tests/Support/VirtualStartupFactory.php');
        parent::setUp();
    }
    /**
     * Build a proxy object, into $this->_proxy to test it
     */
    protected function _buildProxy()
    {
        $this->_proxy = new Support\IntegratedProxy();
    }
}