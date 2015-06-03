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
 *
 * @version     1.1.1
 */

namespace UniAlteri\Tests\Support\Extendable\Mother\States;

use UniAlteri\States\States;

/**
 * State StateTwo
 * Copy from Demo for functional tests.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateTwo extends States\AbstractState
{
    /**
     * @return int
     */
    public function methodPublic()
    {
        return 123;
    }

    /**
     * @return int
     */
    protected function methodProtected()
    {
        return 456;
    }

    /**
     * @return int
     */
    private function methodPrivate()
    {
        return 789;
    }

    /**
     * @return int
     */
    public function methodRecallPrivate()
    {
        return $this->methodPrivate() * 2;
    }
}
