<?php
/**
 * Created by JetBrains PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, agence.net.ua
 * Date: 27/05/13
 * Time: 16:24
 */

namespace UniAlteri\Tests\States\Factory;

use \UniAlteri\States\Factory;
use \UniAlteri\Tests\Support;

class StandardTest extends AbstractFactoryTest
{
    /**
     * Return the Factory Object Interface
     * @return Factory\FactoryInterface
     */
    public function getFactoryObject()
    {
        $factory = new Factory\Standard();
        $factory->setDIContainer($this->_container);
        return $factory;
    }
}

