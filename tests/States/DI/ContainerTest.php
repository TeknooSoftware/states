<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, www.uni-alteri.fr
 * Date: 27/05/13
 * Time: 16:25
 */

namespace UniAlteri\States\DI;

class ContainerTest extends \PHPUnit_Framework_TestCase{

    /**
     * Return a valid container for tests
     * @return ContainerInterface
     */
    protected function _buildContainer(){

    }

    /**
     * @param ContainerInterface $container
     */
    protected function _populateContainer($container){
        $container->register('object1', new \stdClass());
        $container->register('date1', function(){ return new \DateTime();});
        $container->register('array1', '\ArrayObject');
        $container->register('\Exception');
    }

    /**
     * Storage must throw an exception if the name is not valid
     */
    public function testRegisterWithBadName(){
        $container = $this->_buildContainer();
        try{
            $container->register('##', new \stdClass());
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Error, the idenfier must be a valid php var name http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Register into the container the final object to return
     */
    public function testRegisterWithObject(){
        $container = $this->_buildContainer();
        $id = $container->register('object1', new \stdClass());
        $this->assertTrue(is_string($id), 'Error, the unique id of the register instance must be a string');
        $this->assertNotEmpty($id);
    }

    /**
     * Register into the container a closure to call to build the object to return
     */
    public function testRegisterWithClosure(){
        $container = $this->_buildContainer();
        $id = $container->register('date1', function(){ return new \DateTime();});
        $this->assertTrue(is_string($id), 'Error, the unique id of the register instance must be a string');
        $this->assertNotEmpty($id);
    }

    /**
     * Register into the container the class name
     */
    public function testRegisterWithClassName(){
        $container = $this->_buildContainer();
        $id = $container->register('array1', '\ArrayObject');
        $this->assertTrue(is_string($id), 'Error, the unique id of the register instance must be a string');
        $this->assertNotEmpty($id);
    }

    /**
     * Register into the container only the class name as identifier, with no "factory" param
     */
    public function testRegisterWithOnlyClassNameAsIdentifier(){
        $container = $this->_buildContainer();
        $id = $container->register('\Exception');
        $this->assertTrue(is_string($id), 'Error, the unique id of the register instance must be a string');
        $this->assertNotEmpty($id);
    }

    /**
     * Test if each registering ids are differents
     */
    public function testUniqueIdsForRegistering(){
        $container = $this->_buildContainer();
        $array = array(
            $container->register('object1', new \stdClass()),
            $container->register('date1', function(){ return new \DateTime();}),
            $container->register('array1', '\ArrayObject'),
            $container->register('\Exception')
        );

        $this->assertEquals(4, count($array), 'Error, container must registered 4 instances and return 4 differents ids');
    }

    /**
     * Test container to retrieve an instance of the required element, and build it if needed
     */
    public function testGet(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);
        $object = $container->get('object1');
        $this->assertInstanceOf('stdClass', $object);
        $date = $container->get('date1');
        $this->assertInstanceOf('DateTime', $date);
        $array = $container->get('array1');
        $this->assertInstanceOf('ArrayObject', $array);
        $exception = $container->get('Exception');
        $this->assertInstanceOf('\Exception', $exception);
    }

    /**
     * Test if with several gets, the instance returned is the original instance
     */
    public function testSeveralGetsWithObject(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);

        $obj1 = $container->get('object1');
        $obj2 = $container->get('object1');
        $obj3 = $container->get('object1');

        $this->assertSame($obj1, $obj2, 'Error, the container must only build the instance ar first call');
        $this->assertSame($obj1, $obj3, 'Error, the container must only build the instance ar first call');
        $this->assertInstanceOf('stdClass', $obj1);
    }

    /**
     * Test if the factory is a closure, it is called only the first time
     */
    public function testSeveralGetsWithClosure(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);

        $obj1 = $container->get('date1');
        $obj2 = $container->get('date1');
        $obj3 = $container->get('date1');

        $this->assertSame($obj1, $obj2, 'Error, the container must only build the instance ar first call');
        $this->assertSame($obj1, $obj3, 'Error, the container must only build the instance ar first call');
        $this->assertInstanceOf('DateTime', $obj1);
    }

    /**
     * Test if the factory is only the class name, it is builded only the first time
     */
    public function testSeveralGetsWithClassName(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);

        $obj1 = $container->get('array1');
        $obj2 = $container->get('array1');
        $obj3 = $container->get('array1');

        $this->assertSame($obj1, $obj2, 'Error, the container must only build the instance ar first call');
        $this->assertSame($obj1, $obj3, 'Error, the container must only build the instance ar first call');
        $this->assertInstanceOf('ArrayObject', $obj1);
    }

    /**
     * Test if the factory is only the class name as identifier, it is builded only the first type
     */
    public function testSeveralGetsWithClassNameAsIdentifier(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);

        $obj1 = $container->get('Exception');
        $obj2 = $container->get('Exception');
        $obj3 = $container->get('Exception');

        $this->assertSame($obj1, $obj2, 'Error, the container must only build the instance ar first call');
        $this->assertSame($obj1, $obj3, 'Error, the container must only build the instance ar first call');
        $this->assertInstanceOf('\Exception', $obj1);
    }

    /**
     * Test get instance with params to passs to constructor
     */
    public function testGetWithParams(){
        $container = $this->_buildContainer();
        $container->register('closure1', function($a, $b, $c){ return new \ArrayObject(array($a, $b, $c));});

        $obj1 = $container->get('closure1', 1, 2, 3); //First call, return array object [1, 2, 3]
        $obj2 = $container->get('closure1', 4, 5, 6); //Second call, return the previous object, with 1, 2, 3

        $this->assertEquals(
            array(1, 2, 3),
            $obj1->getArrayCopy()
        );

        $this->assertEquals(
            array(1, 2, 3),
            $obj2->getArrayCopy(),
            'Error, the container must not rebuild the instance for second call, even if arguments change'
        );
    }

    /**
     * Test if the params passed to configure is not an array, the container throw an exception
     */
    public function testConfigureBadArray(){
        $container = $this->_buildContainer();
        try{
            $container->configure(new \DateTime());
        }
        catch(\Exception $e){
            return;
        }

        $this->fail('Error, method configure of the container must accept only array an ArrayObject');
    }

    /**
     * Test configuration of the container with an array
     */
    public function testConfigure(){
        $container = $this->_buildContainer();
        $container->configure(
            new \ArrayObject(
                array(
                    'object1'   => new \stdClass(),
                    'date1'     => function(){ return new \DateTime(); },
                    'exception1'=> '\Exception'
                )
            )
        );

        $this->assertInstanceOf('stdClass', $container->get('object1'));
        $this->assertInstanceOf('DateTime', $container->get('date1'));
        $this->assertInstanceOf('Exception', $container->get('exception1'));
    }

    /**
     * Test if the id to unregister is not valid, the container throw an exception
     */
    public function testUnregisterBadId(){
        $container = $this->_buildContainer();
        try{
            $container->unregister('##');
        }
        catch(\Exception $e){
            return true;
        }

        $this->fail('Error, the idenfier must be a valid php var name http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test unregister an instance from the DI
     */
    public function testUnregister(){
        $container = $this->_buildContainer();
        $id = $container->register('object1', new \stdClass());
        $this->assertInstanceOf('stdClass', $container->get('object1'));

        $container->unregister($id);
        try{
            $container->get('object1');
        }
        catch(\Exception $e){
            return;
        }

        $this->fail('Error, if the instance does not exist or it was removed, the container must throw an exception');
    }

    /**
     * Test if the container return all instances with the ids
     */
    public function testListDefinitions(){
        $container = $this->_buildContainer();
        $this->_populateContainer($container);

        $list = $container->listDefinitions();
        $this->assertEquals(
            array_values($list),
            array('object1', 'date1', 'array1', 'Exception'),
            'Error, the container method "listContainer" must return all instance name with there unique id'
        );
    }
}