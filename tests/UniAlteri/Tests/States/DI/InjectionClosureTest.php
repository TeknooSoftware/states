<?php
/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.0.2
 */

namespace UniAlteri\Tests\States\DI;

use UniAlteri\States\DI;
use UniAlteri\States\DI\Exception;
use UniAlteri\Tests\Support;

/**
 * Class InjectionClosureTest
 * Check if the Injection Closure class has the excepted behavior defined by the DI\InjectionClosureInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class InjectionClosureTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Return a valid InjectionClosureInterface object.
     *
     * @param callable $closure
     *
     * @return DI\InjectionClosure
     */
    protected function buildClosure(\Closure $closure = null)
    {
        if (null === $closure) {
            $closure = function () {
                return array_reverse(func_get_args());
            };
        }

        $injectionClosureObject = new DI\InjectionClosure();
        $injectionClosureObject->setClosure($closure);

        return $injectionClosureObject;
    }

    /**
     * Test exception when the Container is not valid when we set a bad object as di container.
     */
    public function testSetDiContainerBad()
    {
        $injectionClosureObject = new DI\InjectionClosure();
        try {
            $injectionClosureObject->setDIContainer(new \DateTime());
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the object must throw an exception when the DI Container is not valid');
    }

    /**
     * Test behavior for methods Set And GetDiContainer.
     */
    public function testSetAndGetDiContainer()
    {
        $object = new DI\InjectionClosure();
        $this->assertNull($object->getDIContainer());
        $virtualContainer = new Support\MockDIContainer();
        $this->assertSame($object, $object->setDIContainer($virtualContainer));
        $this->assertSame($virtualContainer, $object->getDIContainer());
    }

    /**
     * The Injection Closure object must not accept object who not implement \Closure.
     *
     * @return bool
     */
    public function testBadClosureConstruct()
    {
        try {
            $a = new DI\InjectionClosure();
            $a->setClosure(new \stdClass());
        } catch (Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the Injection closure object must throw an exception if the object is not a closure');
    }

    /**
     * Test Injection closure creation.
     */
    public function testCreateClosure()
    {
        $closure = $this->buildClosure();
        $this->assertInstanceOf('\UniAlteri\States\DI\InjectionClosureInterface', $closure);
    }

    /**
     * Test invoking from injection with the closure, execute the closure (the closure test returns arguments order.
     */
    public function testInvokeWithArgs()
    {
        $closure = $this->buildClosure();
        $return = $closure('foo', 'boo', 'hello', 'world');
        $this->assertSame(
            array(
                'world',
                'hello',
                'boo',
                'foo',
            ),
            $return,
            'Error, the closure is not called by the injector '
        );
    }

    /**
     * Test if the injector car return the original closure.
     */
    public function testGetClosure()
    {
        $myClosure = function ($i) {
            return $i+1;
        };

        $injectionClosure = new DI\InjectionClosure($myClosure);
        $this->assertSame($myClosure, $injectionClosure->getClosure());
    }

    /**
     * Storage must throw an exception if the attribute name is not valid.
     */
    public function testSaveBadStaticProperty()
    {
        try {
            $this->buildClosure()->saveProperty('##', 'foo');
        } catch (Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test behavior of injector with static properties.
     */
    public function testGetSaveStaticProperty()
    {
        $closure = $this->buildClosure();
        $closure->saveProperty('static1', 'foo');
        $closure->saveProperty('static2', new \stdClass());

        $this->assertEquals('foo', $closure->getProperty('static1'));
        $obj = $closure->getProperty('static2');
        $this->assertInstanceOf('stdClass', $obj);
        $obj->attr1 = 'boo';
        $this->assertEquals('boo', $closure->getProperty('static2')->attr1);
    }

    /**
     * Storage must throw an exception if the attribute name is not valid.
     */
    public function testGetBadStaticProperty()
    {
        try {
            $this->buildClosure()->saveProperty('##', 'foo');
        } catch (Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test if statics properties are persistent on all call of the closure.
     */
    public function testPersistenceOfStaticProperty()
    {
        $closure = $this->buildClosure();
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
     * Storage must throw an exception if the attribute name is not valid.
     */
    public function testDeleteBadStaticProperty()
    {
        try {
            $this->buildClosure()->deleteProperty('##');
        } catch (Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test deletion.
     */
    public function testDeleteStaticProperty()
    {
        $closure = $this->buildClosure();
        $closure->saveProperty('static1', 'foo');
        $this->assertEquals('foo', $closure->getProperty('static1'));
        $closure->deleteProperty('static1');
        $this->assertNull($closure->getProperty('static1'));
    }

    /**
     * Storage must throw an exception if the attribute name is not valid.
     */
    public function testTestBadStaticProperty()
    {
        try {
            $this->buildClosure()->testProperty('##');
        } catch (Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the storage adapter must throw an exception if the attribute name is not valid : http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test "test" to check if a static property exists.
     */
    public function testTestStaticProperty()
    {
        $closure = $this->buildClosure();
        $this->assertFalse($closure->testProperty('static1'));
        $closure->saveProperty('static1', 'foo');
        $this->assertTrue($closure->testProperty('static1'));
        $closure->deleteProperty('static1');
        $this->assertFalse($closure->testProperty('static1'));
    }
}
