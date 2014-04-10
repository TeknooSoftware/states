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
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support\Loader\Class3;

use \UniAlteri\Tests\Support;

class FactoryClass extends Support\VirtualFactory
{
    /**
     * Throw an exception to test if the loader return false in loading class
     * @param string $statedClassName
     * @param string $path
     * @return bool|void
     * @throws \Exception
     */
    public function initialize($statedClassName, $path)
    {
        throw new \Exception('test');
    }
}