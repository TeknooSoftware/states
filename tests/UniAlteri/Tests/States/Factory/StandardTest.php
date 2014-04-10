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
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\States\Factory;

use \UniAlteri\States\Factory;
use \UniAlteri\Tests\Support;

class StandardTest extends AbstractFactoryTest
{
    /**
     * Return the Factory Object Interface
     * @param boolean $populateContainer to populate di container of this factory
     * @return Factory\FactoryInterface
     */
    public function getFactoryObject($populateContainer=true)
    {
        $factory = new Factory\Standard();
        if (true === $populateContainer) {
            $factory->setDIContainer($this->_container);
        }
        return $factory;
    }
}

