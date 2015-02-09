<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.2
 */
namespace UniAlteri\Tests\States\DI;

use UniAlteri\States\DI;
use UniAlteri\Tests\Support;

/**
 * Class ContainerTest
 * Check if the DI Container has the excepted behavior defined by the interface DI\ContainerInterface
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return a valid container for tests
     * @return DI\ContainerInterface
     */
    protected function buildContainer()
    {
        return new DI\Container();
    }

    /**
     * @param DI\ContainerInterface $container
     */
    protected function populateContainer($container)
    {
        $container->registerInstance('instanceClass', '\DateTime');
        $container->registerInstance('instanceObject', new \DateTime());
        $container->registerInstance('instanceFunction', function () {return new \DateTime();});
        $container->registerService('serviceClass', '\DateTime');
        $container->registerService('serviceObject', new Support\MockInvokableClass());
        $container->registerService('serviceFunction', function () {return new \stdClass();});
    }

    /**
     * The container must accepts only identifier as [a-zA-Z_][a-zA-Z0-9_/]*
     */
    public function testRegisterInstanceBadIdentifier()
    {
        try {
            $this->buildContainer()->registerInstance('##', 'DateTime');
        } catch (DI\Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalName exception');
    }

    /**
     * The container must throws an exception if the class of the instance does not exist
     */
    public function testRegisterInstanceBadClass()
    {
        try {
            $this->buildContainer()->registerInstance('class', 'NonExistentClass');
        } catch (DI\Exception\ClassNotFound $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\ClassNotFound exception');
    }

    /**
     * Test return of registerInstance
     */
    public function testRegisterInstanceClass()
    {
        $container = $this->buildContainer();
        $result = $container->registerInstance('dateObject', '\DateTime');
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerInstance`');
    }

    /**
     * Non invokable object are allowed for instance, but not for service
     */
    public function testRegisterInstanceNonInvokableObject()
    {
        $container = $this->buildContainer();
        $result = $container->registerInstance('dateObject', new \DateTime());
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerInstance`');
    }

    /**
     * Test return of registerInstance
     */
    public function testRegisterInstanceInvokableObject()
    {
        $container = $this->buildContainer();
        $result = $container->registerInstance('dateObject', new \DateTime());
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerInstance`');
    }

    /**
     * Test return of registerInstance
     */
    public function testRegisterInstanceFunction()
    {
        $container = $this->buildContainer();
        $result = $container->registerInstance('dateObject', function () {return new \DateTime();});
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerInstance`');
    }

    /**
     * Test return of registerInstance
     */
    public function testRegisterInstanceArray()
    {
        try {
            $container = $this->buildContainer();
            $container->registerInstance('dateObject', array());
        } catch (DI\Exception\IllegalService $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalService exception if the instance is invalid');
    }

    /**
     * The container must accepts only identifier as [a-zA-Z_][a-zA-Z0-9_/]*
     */
    public function testRegisterServiceBadIdentifier()
    {
        try {
            $this->buildContainer()->registerService('##', 'DateTime');
        } catch (DI\Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalName exception');
    }

    /**
     * The container must throws an exception if the class of the service does not exist
     */
    public function testRegisterServiceBadClass()
    {
        try {
            $this->buildContainer()->registerService('class', 'NonExistentClass');
        } catch (DI\Exception\ClassNotFound $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\ClassNotFound exception');
    }

    /**
     * Test return of registerService
     */
    public function testRegisterServiceClass()
    {
        $container = $this->buildContainer();
        $result = $container->registerService('dateObject', '\DateTime');
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerService`');
    }

    /**
     * Test return of registerService
     */
    public function testRegisterServiceArray()
    {
        try {
            $container = $this->buildContainer();
            $container->registerService('dateObject', array());
        } catch (DI\Exception\IllegalService $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalService exception if the service is invalid');
    }

    /**
     * Test return of registerService with non invokable object
     */
    public function testRegisterServiceNonInvokableObject()
    {
        try {
            $container = $this->buildContainer();
            $container->registerService('dateObject', new \DateTime());
        } catch (DI\Exception\IllegalService $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalService exception if the object is not invokable');
    }

    /**
     * Test return of registerService
     */
    public function testRegisterServiceInvokableObject()
    {
        $container = $this->buildContainer();
        $result = $container->registerService('dateObject', new Support\MockInvokableClass());
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerService`');
    }

    /**
     * Test return of registerService
     */
    public function testRegisterServiceFunction()
    {
        $container = $this->buildContainer();
        $result = $container->registerService('dateObject', function () {return new \DateTime();});
        $this->assertSame($container, $result, 'Error, the container must return $this after `registerService`');
    }

    /**
     * The container must accepts only identifier as [a-zA-Z_][a-zA-Z0-9_/]*
     */
    public function testTestInstanceBadIdentifier()
    {
        try {
            $this->buildContainer()->testEntry('##');
        } catch (DI\Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalName exception');
    }

    /**
     * test behavior of testInstance(), return true if an instance of service exist
     */
    public function testTestInstance()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);
        $this->assertTrue($container->testEntry('instanceClass'));
        $this->assertTrue($container->testEntry('instanceObject'));
        $this->assertTrue($container->testEntry('instanceFunction'));
        $this->assertTrue($container->testEntry('serviceClass'));
        $this->assertTrue($container->testEntry('serviceObject'));
        $this->assertTrue($container->testEntry('serviceFunction'));
        $this->assertFalse($container->testEntry('foo'));
    }

    /**
     * The container must accepts only identifier as [a-zA-Z_][a-zA-Z0-9_/]*
     */
    public function testGetBadIdentifier()
    {
        try {
            $this->buildContainer()->get('##');
        } catch (DI\Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\IllegalName exception');
    }

    /**
     * The container must throw the exception Exception\InvalidArgument when the element is not registered
     */
    public function testGetNotRegistered()
    {
        try {
            $this->buildContainer()->get('unknown');
        } catch (DI\Exception\InvalidArgument $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the container object must throws an Exception\InvalidArgument exception');
    }

    /**
     * Test to get an instance
     */
    public function testGetInstanceClass()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('instanceClass');
        $obj2 = $container->get('instanceClass');
        $this->assertEquals(get_class($obj1), get_class($obj2), 'Error, container, must return the same object for a registered instance');
        $this->assertSame($obj1, $obj2, 'Error, container, must return the same object for a registered instance');
    }

    /**
     * Test to get an instance
     */
    public function testGetInstanceObject()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('instanceObject');
        $obj2 = $container->get('instanceObject');
        $this->assertEquals(get_class($obj1), get_class($obj2), 'Error, container, must return the same object for a registered instance');
        $this->assertSame($obj1, $obj2, 'Error, container, must return the same object for a registered instance');
    }

    /**
     * Test to get an instance
     */
    public function testGetInstanceFunction()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('instanceFunction');
        $obj2 = $container->get('instanceFunction');
        $this->assertEquals(get_class($obj1), get_class($obj2), 'Error, container, must return the same object for a registered instance');
        $this->assertSame($obj1, $obj2, 'Error, container, must return the same object for a registered instance');
        $this->assertInstanceOf('DateTime', $obj1);
    }

    /**
     * Test to get a service behavior
     */
    public function testGetServiceClass()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('serviceClass');
        $obj2 = $container->get('serviceClass');
        $this->assertEquals(get_class($obj1), get_class($obj2), 'Error, container, must return the same object for a registered instance');
        $this->assertNotSame($obj1, $obj2, 'Error, container, must return two different objects for a same service');
        $this->assertEquals('DateTime', get_class($obj1));
    }

    /**
     * Test to get a service behavior for invokable
     */
    public function testGetServiceObject()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('serviceObject');
        $this->assertInstanceOf('\stdClass', $obj1, 'Error, for a service, the invokable object must be called and not returned');
    }

    /**
     * Test to get a service behavior for anonymous function
     */
    public function testGetServiceFunction()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $obj1 = $container->get('serviceFunction');
        $this->assertInstanceOf('\stdClass', $obj1, 'Error, for a service, the invokable object must be called and not returned');
    }

    /**
     * Test if the params passed to configure is not an array, the container throw an exception
     */
    public function testConfigureBadArray()
    {
        $container = $this->buildContainer();
        try {
            $container->configure(new \DateTime());
        } catch (DI\Exception\InvalidArgument $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, method configure of the container must accept only array an ArrayObject');
    }

    /**
     * Test configuration of the container with an array
     */
    public function testConfigure()
    {
        $container = $this->buildContainer();
        $container->configure(
            array(
                'instances' => array(
                    'instanceClass'  => '\ArrayObject',
                    'date1'         => function () { return new \DateTime(); },
                ),
                'services'  => array(
                    'stdClass'         => function () { return new \stdClass(); },
                ),
            )
        );

        $this->assertInstanceOf('\ArrayObject', $container->get('instanceClass'));
        $this->assertInstanceOf('\DateTime', $container->get('date1'));
        $this->assertInstanceOf('\stdClass', $container->get('stdClass'));
    }

    /**
     * Test if the id to unregister is not valid, the container throw an exception
     */
    public function testUnregisterBadId()
    {
        $container = $this->buildContainer();
        try {
            $container->unregister('##');
        } catch (DI\Exception\IllegalName $exception) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the identifier must be a valid php var name http://www.php.net/manual/en/language.variables.basics.php');
    }

    /**
     * Test unregister an instance from the DI
     */
    public function testUnregister()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);
        $this->assertTrue($container->testEntry('instanceObject'));

        $container->unregister('instanceObject');
        $this->assertFalse($container->testEntry('instanceObject'));
    }

    /**
     * Test if the container return all instances with the ids
     */
    public function testListDefinitions()
    {
        $container = $this->buildContainer();
        $this->populateContainer($container);

        $list = $container->listDefinitions();
        $this->assertEquals(
            array_values($list),
            array('instanceClass', 'instanceObject', 'instanceFunction', 'serviceClass', 'serviceObject', 'serviceFunction'),
            'Error, the container method "listContainer" must return all instance name with there unique id'
        );
    }

    /**
     * Test cloning behavior : Previous service/instance are shared between two cloned instance
     * But, each container progresses independently (can update, add or remove instance or service)
     */
    public function testCloning()
    {
        $originalContainer = $this->buildContainer();
        $stdObject = new \stdClass();
        $stdObject->foo = 'bar';
        $originalContainer->registerInstance('object', $stdObject);
        $originalContainer->registerService('date', function () {
            return new \DateTime();
        });

        $this->assertSame($stdObject, $originalContainer->get('object'));
        $this->assertInstanceOf('\DateTime', $originalContainer->get('date'));

        $clonedContainer = clone $originalContainer;
        $this->assertSame($stdObject, $clonedContainer->get('object'));
        $this->assertInstanceOf('\DateTime', $clonedContainer->get('date'));

        $stdObject2 = new \stdClass();
        $stdObject2->bar = 'foo';
        $clonedContainer->registerInstance('object2', $stdObject2);
        $this->assertSame($stdObject2, $clonedContainer->get('object2'));
        $this->assertFalse($originalContainer->testEntry('object2'));

        $clonedContainer->unregister('date');
        $clonedContainer->registerInstance('date', function () {
            return 123;
        });

        $this->assertSame(123, $clonedContainer->get('date'));
        $this->assertInstanceOf('\DateTime', $originalContainer->get('date'));
    }
}
