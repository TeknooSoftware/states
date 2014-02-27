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
}