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
 */

namespace UniAlteri\States\Proxy;

/**
 * Trait IntegratedTrait
 * Trait to use with an integrated proxy to allow developer to create an instance a stated class
 * like another class with the operator new.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
trait IntegratedTrait
{
    /**
     * Method called by constructor to initialize this object from the stated class's factory.
     *
     * @api
     *
     * @throws Exception\IllegalFactory
     * @throws Exception\UnavailableFactory
     */
    protected function initializeObjectWithFactory()
    {
        //Check if the startup class exists
        if (!class_exists(static::$startupFactoryClassName, true)) {
            throw new Exception\UnavailableFactory('Error, the startup factory is not available');
        }

        //Check if the startup class implements the interface 'UniAlteri\States\Factory\StartupFactoryInterface'
        $interfacesImplementedArray = array_flip(//Do a flip because isset is more effecient than in_array
            class_implements(static::$startupFactoryClassName)
        );

        if (!isset($interfacesImplementedArray['UniAlteri\States\Factory\StartupFactoryInterface'])) {
            throw new Exception\IllegalFactory('Error, the startup factory does not implement the startup interface');
        }

        //Call the startup factory
        $reflectionMethod = new \ReflectionMethod(static::$startupFactoryClassName, 'forwardStartup');
        $reflectionMethod->invoke(null, $this);
    }
}
