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
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\Proxy;

class IntegratedProxy extends Proxy\Integrated
{
    /**
     * Class name of the factory to use during set up to initialize this object
     * @var string
     */
    protected static $_startupFactoryClassName = '\UniAlteri\Tests\Support\VirtualStartupFactory';

    /**
     * Method to update static::$_startupFactoryClassName to run unit test
     * @param string $className
     */
    public static function defineStartupFactoryClassName($className)
    {
        static::$_startupFactoryClassName = $className;
    }
}