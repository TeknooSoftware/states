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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\Tests\States\Factory;

use Teknoo\States\Factory;

/**
 * Class IntegratedTest
 * Test the exception behavior of the integrated factory.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class IntegratedTest extends AbstractFactoryTest
{
    /**
     * Return the Factory Object Interface.
     *
     * @param bool $populateContainer to populate di container of this factory
     *
     * @return Factory\FactoryInterface
     */
    public function getFactoryObject($populateContainer = true)
    {
        $factory = new Factory\Integrated();
        if (true === $populateContainer) {
            $factory->setDIContainer($this->container);
        }

        return $factory;
    }

    /**
     * Test if the factory Integrated initialize the StartupFactory.
     */
    public function testInitialization()
    {
        Factory\StandardStartupFactory::reset();
        $factory = $this->getFactoryObject(true);
        $factory->initialize('foo', 'bar');
        $this->assertEquals(
            array(
                'foo\\foo',
            ),
            Factory\StandardStartupFactory::listRegisteredFactory()
        );
    }
}
