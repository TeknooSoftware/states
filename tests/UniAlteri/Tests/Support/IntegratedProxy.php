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

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\Proxy;

/**
 * Class IntegratedProxy
 * To build an specific instance of the class Proxy\Integrated to test this default class.
 * By default, the class Proxy\Integrated uses '\UniAlteri\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\UniAlteri\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */
class IntegratedProxy extends Proxy\Integrated
{
    /**
     * Class name of the factory to use during set up to initialize this object.
     * It is a virtual factory, it does nothing except logs actions
     * @var string
     */
    protected static $_startupFactoryClassName = '\UniAlteri\Tests\Support\MockStartupFactory';

    /**
     * Method to update static::$_startupFactoryClassName to run some unit tests
     * @param string $className
     */
    public static function defineStartupFactoryClassName($className)
    {
        static::$_startupFactoryClassName = $className;
    }
}