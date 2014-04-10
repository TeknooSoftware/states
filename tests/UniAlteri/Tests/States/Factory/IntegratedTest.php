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
 * @link        http://teknoo.it/states Project website
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\States\Factory;

use \UniAlteri\States\Factory;
use \UniAlteri\Tests\Support;

class IntegratedTest extends AbstractFactoryTest
{
    /**
     * Return the Factory Object Interface
     * @param boolean $populateContainer to populate di container of this factory
     * @return Factory\FactoryInterface
     */
    public function getFactoryObject($populateContainer=true)
    {
        $factory = new Factory\Integrated();
        if (true === $populateContainer) {
            $factory->setDIContainer($this->_container);
        }
        return $factory;
    }

    /**
     * Test if the factory Integrated initialize the StartupFactory
     */
    public function testInitialization()
    {
        Factory\StandardStartupFactory::reset();
        $factory = $this->getFactoryObject(true);
        $factory->initialize('foo', 'bar');
        $this->assertEquals(
            array(
                'foo\\foo'
            ),
            Factory\StandardStartupFactory::listRegisteredFactory()
        );
    }
}

