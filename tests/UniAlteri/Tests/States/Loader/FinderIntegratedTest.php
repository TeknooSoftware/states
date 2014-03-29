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

namespace UniAlteri\Tests\States\Loader;

use UniAlteri\States\Loader;
use UniAlteri\States\Factory;
use UniAlteri\States\States;
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

class FinderIntegratedTest extends FinderStandardTest
{

    /**
     * Initialize the finder for test
     * @param string $statedClassName
     * @param string $pathString
     * @return Loader\FinderStandard
     */
    protected function _initializeFind($statedClassName, $pathString)
    {
        $virtualDIContainer = new Support\VirtualDIContainer();
        $this->_finder = new Loader\FinderIntegrated($statedClassName, $pathString);
        $this->_finder->setDIContainer($virtualDIContainer);
        return $this->_finder;
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = new Loader\FinderIntegrated('', '');
        try {
            $object->setDIContainer(new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the DI Container is not valid');
    }

    /**
     * Test behavior for methods Set And GetDiContainer
     */
    public function testSetAndGetDiContainer()
    {
        $object = new Loader\FinderIntegrated('', '');
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\VirtualDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    public function testBuildProxyDefault()
    {
        Factory\StandardStartupFactory::registerFactory('UniAlteri\States\Proxy\Integrated', new Support\VirtualFactory());
        parent::testBuildProxyDefault();
    }
}