<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 17/10/13
 * Time: 10:48
 */

namespace UniAlteri\States\States;

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class  OnlyPublic extends StateAbstract{
    /**
     * Method 1
     */
    public function method1(){}
    public function method2(){}
    public function method3(){}

    public static function static1(){}

    /**
     * Static 2
     */
    public static function static2(){}
    public static function static3(){}
}

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyProtected extends StateAbstract{
    protected  function method4(){}

    /**
     * Description 5
     */
    protected  function method5(){}

    /**
     * Static 4
     */
    protected  static function static4(){}
    protected  static function static5(){}
}

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyPrivate extends StateAbstract{
    private function method6(){}

    /**
     * Method 7
     */
    private function method7(){}
    private function method8(){}
    private function method9(){}

    /**
     * Static 6
     */
    private static function static6(){}
    private static function static7(){}
    private static function static8(){}
    private static function static9(){}
}

abstract class AbstractTest extends \PHPUnit_Framework_TestCase{

    /**
     * @return OnlyPublic
     */
    protected function _getPublicClassObject(){
        return new OnlyPublic();
    }

    /**
     * @return OnlyProtected
     */
    protected function _getProtectedClassObject(){
        return new OnlyProtected();
    }

    /**
     * @return OnlyPrivate
     */
    protected function _getPrivateClassObject(){
        return new OnlyPrivate();
    }

    public function testListMethodsPublic(){
        $this->assertEquals(
            array(
                'method1',
                'method2',
                'method3'
            ),
            $this->_getPublicClassObject()->listMethods()
        );
    }

    public function testListMethodsProtected(){
        $this->assertEquals(
            array(
                'method4',
                'method5',
            ),
            $this->_getProtectedClassObject()->listMethods()
        );
    }

    public function testListMethodsPrivate(){
        $this->assertEquals(
            array(
                'method6',
                'method7',
                'method8',
                'method9',
            ),
            $this->_getPrivateClassObject()->listMethods()
        );
    }

    public function testListStaticMethodsPublic(){
        $this->assertEquals(
            array(
                'static1',
                'static2',
                'static3'
            ),
            $this->_getPublicClassObject()->listStaticMethods()
        );
    }

    public function testListStaticMethodsProtected(){
        $this->assertEquals(
            array(
                'static4',
                'static5',
            ),
            $this->_getProtectedClassObject()->listMethods()
        );
    }

    public function testListStaticMethodsPrivate(){
        $this->assertEquals(
            array(
                'static6',
                'static7',
                'static8',
                'static9',
            ),
            $this->_getPrivateClassObject()->listMethods()
        );
    }

    public function testGetBadMethodDescription(){
        try{
            $this->_getPublicClassObject()->getMethodDescription('badMethod');
        }
        catch(\Exception $e){
            return;
        }

        $this->fail('Error, the state must throws an exception if we require a description of inexistant method');
    }

    public function testGetMethodDescription(){
        $this->assertSame('Method 1', $this->_getPublicClassObject()->getMethodDescription('method1'));
        $this->assertSame('', $this->_getPublicClassObject()->getMethodDescription('method2'));
        $this->assertSame('', $this->_getPublicClassObject()->getMethodDescription('static1'));
        $this->assertSame('Static 2', $this->_getPublicClassObject()->getMethodDescription('static2'));

        $this->assertSame('', $this->_getProtectedClassObject()->getMethodDescription('method4'));
        $this->assertSame('Description 5', $this->_getProtectedClassObject()->getMethodDescription('method5'));
        $this->assertSame('Static 4', $this->_getProtectedClassObject()->getMethodDescription('static4'));
        $this->assertSame('', $this->_getProtectedClassObject()->getMethodDescription('static5'));

        $this->assertSame('', $this->_getPrivateClassObject()->getMethodDescription('method6'));
        $this->assertSame('Method 7', $this->_getPrivateClassObject()->getMethodDescription('method7'));
        $this->assertSame('Static 6', $this->_getPrivateClassObject()->getMethodDescription('static6'));
        $this->assertSame('', $this->_getPrivateClassObject()->getMethodDescription('static7'));
    }

    public function testGetBadClosure(){
        try{
            $this->_getPublicClassObject()->getClosure('badMethod');
        }
        catch(\Exception $e){
            return;
        }

        $this->fail('Error, the state must throws an exception if we require a description of inexistant method');
    }

    public function testGetClosure(){
        $closure1 = $this->_getPublicClassObject()->getMethodDescription('method1');
        $this->assertInstanceOf('Closure', $closure1);
        $closure2 = $this->_getPublicClassObject()->getMethodDescription('static1');
        $this->assertInstanceOf('Closure', $closure2);

        $closure4 = $this->_getProtectedClassObject()->getMethodDescription('method4');
        $this->assertInstanceOf('Closure', $closure4);
        $closure5 = $this->_getProtectedClassObject()->getMethodDescription('static5');
        $this->assertInstanceOf('Closure', $closure5);

        $closure6 = $this->_getPrivateClassObject()->getMethodDescription('method6');
        $this->assertInstanceOf('Closure', $closure6);
        $closure7 = $this->_getPrivateClassObject()->getMethodDescription('static7');
        $this->assertInstanceOf('Closure', $closure7);
    }
}
