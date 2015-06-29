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

namespace Acme\Extendable\GrandDaughter;

use Acme\Extendable\Daughter\Daughter;

/**
 * Proxy GrandDaughter
 * Proxy class of the stated class GrandDaughter.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class GrandDaughter extends Daughter
{
    /**
     * Return the list of available state in this class.
     *
     * @return array
     */
    public function listMethodsByStates()
    {
        $methodsList = array();
        foreach ($this->getStatesList() as $stateName => $stateContainer) {
            $methodsList[$stateName] = $stateContainer->listMethods()->getArrayCopy();
        }

        ksort($methodsList);

        return $methodsList;
    }
}
