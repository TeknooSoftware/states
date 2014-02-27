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
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Proxy\Exception;

use \UniAlteri\States\Exception;

/**
 * Class AvailableSeveralMethodImplementations
 * @package UniAlteri\States\Proxy\Exception
 * Exception threw when a stated object is having several implementations a the calling method
 * in different enabled states
 */
class AvailableSeveralMethodImplementations extends Exception\AvailableSeveralMethodImplementations
{
}