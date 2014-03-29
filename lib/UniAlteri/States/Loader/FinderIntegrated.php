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
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;

use \UniAlteri\States\Loader\Exception;
use \UniAlteri\States\DI;
use \UniAlteri\States\States;
use \UniAlteri\States\Proxy;

/**
 * Class FinderIntegrated
 * @package UniAlteri\States\Loader
 * Implementation of the finder. It is used with this library to find and load
 * from each stated class all states and the proxy.
 * Extend FinderStandard to use '\UniAlteri\States\Proxy\Integrated' instead of
 * '\UniAlteri\States\Proxy\Standard'
 */
class FinderIntegrated extends FinderStandard
{
    /**
     * Default proxy class name to use when there are no proxy class name
     * @var string
     */
    protected $_defaultProxyClassName = '\UniAlteri\States\Proxy\Integrated';


}