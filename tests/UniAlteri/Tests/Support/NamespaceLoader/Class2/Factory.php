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
 *
 * Mock factory file to test loader behavior when the factory class is valid.
 * This factory is included from namespace definitions registered into loader.
 * The path is not computed from the class's name following PSR-0 recommendations
 */

namespace UniAlteri\Tests\Support\Loader\Class2;

use \UniAlteri\Tests\Support;

/**
 * Class FactoryClass
 * Mock factory file to test loader behavior when the factory class is valid.
 * This factory is included from namespace definitions registered into loader.
 * The path is not computed from the class's name following PSR-0 recommendations
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class FactoryClass extends Support\MockFactory
{
}