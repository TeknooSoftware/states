<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, www.uni-alteri.fr
 * Date: 27/05/13
 * Time: 16:25
 */

namespace UniAlteri\States\AutoLoader;

class LoaderTest extends \PHPUnit_Framework_TestCase{
    /**
     * @var AutoLoaderInterface
     */
    protected $_loader = null;

    /**
     * Prepare environment before test
     */
    protected function setUp(){
        parent::setUp();
    }

    /**
     * Clean environment after test
     */
    protected function tearDown(){
        parent::tearDown();
    }

    /**
     * Must throw an exception if the path is not valid
     */
    public function testNonValidAddIncludePath(){
        try{
            $this->_loader->addIncludePath('$$$');
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Loader must throw an exception if the path is invalid (non asci)');
    }

    /**
     * Must return false
     */
    public function testAddNonExistentIncludePath(){
        $this->assertFalse($this->_loader->addIncludePath('I/Do/Not/Exist'));
    }

    /**
     * Must return true
     */
    public function testAddValidIncludePath(){
        $this->assertTrue($this->_loader->addIncludePath(SUPPORT_PATH.'/AutoLoader/Factory'));
    }

    /**
     * Must throw an exception if the class name is not valid
     */
    public function testNonValidClass(){
        try{
            $this->_loader->classLoader('$$$');
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Loader must throw an exception if the class name is not a valid : http://www.php.net/manual/en/language.oop5.basic.php');
    }

    /**
     * Must return false
     */
    public function testNonExistentClass(){
        $this->assertFalse($this->_loader->classLoader('ClassDoesNotExist'));
    }

    /**
     * Must return true
     */
    public function testValidClass(){
        $this->assertTrue($this->_loader->classLoader('ClassExist'));
    }

    /**
     * Must return true and not reinclude the class
     */
    public function testAlreadyLoadedClass(){
        $this->assertTrue($this->_loader->classLoader('ClassExist'));
        $this->assertTrue($this->_loader->classLoader('ClassExist'));
    }

    /**
     * Must throw an exception if the class name is not valid
     */
    public function testNonValidFactory(){
        try{
            $this->_loader->factoryLoader('$$$');
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Loader must throw an exception if the class name is not a valid : http://www.php.net/manual/en/language.oop5.basic.php');
    }

    /**
     * Must throw an exception if the class name is not valid
     */
    public function testNonValidInterfaceFactory(){
        try{
            $this->_loader->factoryLoader('FactoryWithoutInterface');
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Loader must throw an exception if the factory not implement the good interface');
    }

    /**
     * Must create dynamically the factory and implement
     */
    public function testNonExistentFactory(){
        $this->assertFalse(class_exists('FactoryNDoesNotExist', true));
        $this->assertTrue($this->_loader->factoryLoader('FactoryNDoesNotExist'));
        $interfaces = class_implements('FactoryNDoesNotExist');
        $this->assertTrue(in_array('FactoryInterface', $interfaces), 'Error the new factory must implement the interface');
    }

    /**
     * Must return true
     */
    public function testValidFactory(){
        $this->assertFalse($this->_loader->factoryLoader('FactoryExist'));
    }

    /**
     * Must return true and not reinclude the class
     */
    public function testAlreadyLoadedFactory(){
        $this->assertFalse($this->_loader->factoryLoader('FactoryExist'));
        $this->assertFalse($this->_loader->factoryLoader('FactoryExist'));
    }
}