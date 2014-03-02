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

use \UniAlteri\States\States;

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyProtected extends States\AbstractState
{
    /**
     * To simulate a real state behavior
     * @param boolean $initializeContainer initialize virtual di container for state
     */
    public function __construct($initializeContainer=true)
    {
        if (true === $initializeContainer) {
            $this->setDIContainer(new VirtualDIContainer());
            $this->getDIContainer()->registerService(
                States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER,
                function() {
                    return new VirtualInjectionClosure();
                }
            );
        }
    }

    protected static function _staticMethod5()
    {
    }

    /**
     * Standard Method 6
     * @param $a
     * @param $b
     * @return mixed
     */
    protected function _standardMethod6($a, $b)
    {
        return $a+$b;
    }

    /**
     * Final Method 7
     */
    final protected function _finalMethod7()
    {
    }

    protected function _standardMethod8()
    {
    }
}