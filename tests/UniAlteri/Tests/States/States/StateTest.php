<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

namespace UniAlteri\Tests\States\States;

use \UniAlteri\States\Proxy;
use \UniAlteri\Tests\Support;

/**
 * Class StateTest
 * Implementation of AbstractStatesTest to test the trait \UniAlteri\States\States\TraitState and
 * the abstract class \UniAlteri\States\States\AbstractState
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateTest extends AbstractStatesTest
{
    /**
     * Build a basic object to provide only public methods
     * @param  boolean                $initializeContainer initialize virtual di container for state
     * @return Support\MockOnlyPublic
     */
    protected function getPublicClassObject($initializeContainer=true)
    {
        return new Support\MockOnlyPublic($initializeContainer);
    }

    /**
     * Build a basic object to provide only protected methods
     * @param  boolean                   $initializeContainer initialize virtual di container for state
     * @return Support\MockOnlyProtected
     */
    protected function getProtectedClassObject($initializeContainer=true)
    {
        return new Support\MockOnlyProtected($initializeContainer);
    }

    /**
     * Build a basic object to provide only private methods
     * @param  boolean                 $initializeContainer initialize virtual di container for state
     * @return Support\MockOnlyPrivate
     */
    protected function getPrivateClassObject($initializeContainer=true)
    {
        return new Support\MockOnlyPrivate($initializeContainer);
    }

    /**
     * Build a virtual proxy for test
     * @return Proxy\ProxyInterface
     */
    protected function getMockProxy()
    {
        return new Support\MockProxy(array());
    }
}
