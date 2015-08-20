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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Factory;

/**
 * Class Integrated
 * Implementation of the stated object factory to use with this library to build a new instance.
 * It is an alternative of Standard factory to allow developers to use the operator `new` with stated classes.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Integrated implements FactoryInterface
{
    use FactoryTrait {
        //Rename the initialize method of the trait to override it into this class.
        FactoryTrait::initialize as traitInitialize;
    }

    /**
     * It registers the class name in the factory, it retrieves the finder object and load the proxy from the finder.
     *
     * @param string $statedClassName the name of the stated class
     *
     * @return FactoryInterface
     */
    protected function initialize(\string $statedClassName): FactoryInterface
    {
        //Call trait's method to initialize this stated class
        $this->traitInitialize($statedClassName);

        //Build the factory identifier (the proxy class name)
        $parts = explode('\\', $statedClassName);
        $statedClassName .= '\\'.array_pop($parts);

        //Register this factory into the startup factory
        StandardStartupFactory::registerFactory($statedClassName, $this);

        return $this;
    }
}
