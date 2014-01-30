<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 27/05/13
 * Time: 16:25
 */

namespace UniAlteri\States\DI;

use UniAlteri\States\DI;

class InjectionClosureTest extends \PHPUnit_Framework_TestCase{

    protected function setUp(){
        parent::setUp();
    }

    protected function tearDown(){
        parent::tearDown();
    }

    /**
     * Return a valid InjectionClosureInterface object
     * @param callable $closure
     * @return DI\InjectionClosure
     */
    protected function _buildClosure(\Closure $closure=null){
        if(null === $closure){
            $closure = function(){
                return array_reverse(func_get_args());
            };
        }

        $injectionClosureObject = new InjectionClosure();
        $injectionClosureObject->setClosure($closure);
        return $injectionClosureObject;
    }

    /**
     * The Injection Closure object must not accept object who not implement \Closure
     * @return bool
     */
    public function testBadClosureConstruct(){
        try{
            $a = new InjectionClosure();
            $a->setClosure(new \stdClass());
        }
        catch(Exception\InvalidArgument $exception){
            return;
        }
        catch(\Exception $e){}

        $this->fail('Error, the Injection closure object must throw an exception if the object is not a closure');
    }

    /**
     * Test Injection closure creation
     */
    public function testCreateClosure(){
        $closure = $this->_buildClosure();
        $this->assertInstanceOf('InjectionClosureInterface', $closure);
    }

    /**
     * Test invokation from injection with the closure, execute the closure (the closure test returns arguments order
     */
    public function testInvokeWithArgs(){
        $closure = $this->_buildClosure();
        $return = $closure('foo', 'boo', 'hello', 'world');
        $this->assertSame(
            array(
                'world',
                'hello',
                'boo',
                'foo'
            ),
            $return,
            'Error, the closure is not called by the injector '
        );
    }

    /**
     * Test if the injector car return the original closure
     */
    public function testGetClosure(){
        $myClosure = function($i){return $i+1;};
        $injectionClosure = new InjectionClosure($myClosure);
        $this->assertSame($myClosure, $injectionClosure->getClosure());
    }

    /**
     * Storage must throw an exception if the attribute name is not valid
     */
    public function testSaveBadStaticProperty(){
        try{
            $this->_buildClosure()->saveProperty('##', 'foo');
        }
        catch(Exception\IllegalName $exception){
            return;
        }
        catch(\Exception $e){}

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test behavior of injector with static properties
     */
    public function testGetSaveStaticProperty(){
        $closure = $this->_buildClosure();
        $closure->saveProperty('static1', 'foo');
        $closure->saveProperty('static2', new \stdClass());

        $this->assertEquals('foo', $closure->getProperty('static1'));
        $obj = $closure->getProperty('static2');
        $this->assertInstanceOf('stdClass', $obj);
        $obj->attr1 = 'boo';
        $this->assertEquals('boo', $closure->getProperty('static2')->attr1);
    }

    /**
     * Storage must throw an exception if the attribute name is not valid
     */
    public function testGetBadStaticProperty(){
        try{
            $this->_buildClosure()->saveProperty('##', 'foo');
        }
        catch(Exception\IllegalName $exception){
            return;
        }
        catch(\Exception $e){}

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test if statics properties are persistent on all call of the closure
     */
    public function testPersistenceOfStaticProperty(){
        $closure = $this->_buildClosure();
        $closure->saveProperty('static1', 'foo');
        $result = $closure(1, 2, 3);
        $this->assertEquals(array(3, 2, 1), $result);
        $this->assertEquals('foo', $closure->getProperty('static1'));

        $result = $closure(4, 5, 6);
        $this->assertEquals(array(6, 5, 4), $result);
        $this->assertEquals('foo', $closure->getProperty('static1'));

        $closure->saveProperty('static1', 'boo');
        $result = $closure(7, 8, 9);
        $this->assertEquals(array(9, 8, 7), $result);
        $this->assertEquals('boo', $closure->getProperty('static1'));
    }

    /**
     * Storage must throw an exception if the attribute name is not valid
     */
    public function testDeleteBadStaticProperty(){
        try{
            $this->_buildClosure()->deleteProperty('##');
        }
        catch(Exception\IllegalName $exception){
            return;
        }
        catch(\Exception $e){}

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test deletion
     */
    public function testDeleteStaticProperty(){
        $closure = $this->_buildClosure();
        $closure->saveProperty('static1', 'foo');
        $this->assertEquals('foo', $closure->getProperty('static1'));
        $closure->deleteProperty('static1');
        $this->assertNull($closure->getProperty('static1'));
    }

    /**
     * Storage must throw an exception if the attribute name is not valid
     */
    public function testTestBadStaticProperty(){
        try{
            $this->_buildClosure()->testProperty('##');
        }
        catch(Exception\IllegalName $exception){
            return;
        }
        catch(\Exception $e){}

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test "test" to check if a static property exists
     */
    public function testTestStaticProperty(){
        $closure = $this->_buildClosure();
        $this->assertFalse($closure->testProperty('static1'));
        $closure->saveProperty('static1', 'foo');
        $this->assertTrue($closure->testProperty('static1'));
        $closure->deleteProperty('static1');
        $this->assertFalse($closure->testProperty('static1'));
    }
}