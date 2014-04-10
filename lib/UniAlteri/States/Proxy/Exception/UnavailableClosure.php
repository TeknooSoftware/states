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
 * @subpackage  Proxy
 * @category    Exception
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Proxy\Exception;

use \UniAlteri\States\Exception;

/**
 * Class UnavailableClosure
 * @package UniAlteri\States\Proxy\Exception
 * Exception threw when the proxy requires a closure from a state (to use static behavior)
 * but there are no available state.
 */
class UnavailableClosure extends Exception\UnavailableClosure
{
}