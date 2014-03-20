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
use UniAlteri\States\Loader\Exception;
use UniAlteri\Tests\Support;

class LoaderStandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loader\LoaderInterface
     */
    protected $_loader = null;

    /**
     * Prepare environment before test
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Clean environment after test
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Load object to test it
     * @return Loader\LoaderStandard
     */
    protected function _initializeLoader()
    {
        $this->_loader = new Loader\LoaderStandard();
        return $this->_loader;
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container
     */
    public function testSetDiContainerBad()
    {
        $object = new Loader\LoaderStandard();
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
        $object = new Loader\LoaderStandard();
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\VirtualDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    public function testLoadClassNonExistent()
    {
        $this->assertFalse($this->_initializeLoader()->loadClass('badName'));
    }

    public function testAddIncludePathbadDir()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->addIncludePath('badPath');
        } catch (Exception\UnavailablePath $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if the path to include is unavailable, the loader must throws the exception Exception\UnavailablePath');
    }

    public function testAddIncludePath()
    {
        $this->markTestSkipped(); //todo
    }

    public function testRegisterNamespaceBadName()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->registerNamespace('badNamespace', 'badPath');
        } catch (Exception\UnavailablePath $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if the path of namespace to register is unavailable, the loader must throws the exception Exception\UnavailablePath');
    }

    public function testRegisterNamespace()
    {
        $this->markTestSkipped(); //todo
    }

    public function testRegisterNamespaceMultiplePath()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadClassRestoreOldIncludedPathAfterCalling()
    {
        $this->markTestSkipped(); //todo
    }

    public function testLoadClassRestoreOldIncludedPathAfterException()
    {
        $this->markTestSkipped(); //todo
    }

    public function testBuildFactoryNonExistentFactory()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->buildFactory('badFactory', 'statedClassName');
        } catch (Exception\UnavailableFactory $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if factory\'s class was not found, Loader must throws the exception Exception\UnavailableFactory');
    }

    public function testBuildFactoryBadFactory()
    {
        $loader = $this->_initializeLoader();
        try {
            $loader->buildFactory('stdClass', 'statedClassName');
        } catch (Exception\IllegalFactory $e) {
            return;
        } catch (\Exception $e){ }

        $this->fail('Error, if factory\'s class does not implement the factory interface, Loader must throws the exception Exception\IllegalFactory');
    }

    public function testBuildFactory()
    {
        $loader = $this->_initializeLoader();
        $this->assertEquals(array(), Support\VirtualFactory::listInitializedFactories());
        $factory = $loader->buildFactory('\UniAlteri\Tests\Support\VirtualFactory', 'class1');
        $this->assertEquals(array('class1'), Support\VirtualFactory::listInitializedFactories());
        $factory = $loader->buildFactory('\UniAlteri\Tests\Support\VirtualFactory', 'class2');
        $this->assertEquals(array('class1', 'class2'), Support\VirtualFactory::listInitializedFactories());
        $factory = $loader->buildFactory('\UniAlteri\Tests\Support\VirtualFactory', 'class1');
        $this->assertEquals(
            array('class1', 'class2', 'class1'),
            Support\VirtualFactory::listInitializedFactories(),
            'Error, the loader must not manage factory building. If a even stated class is initialized several times, the loader must call the factory each time. '
        );
    }
}