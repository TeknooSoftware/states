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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support\Extendable\Mother\States;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State StateTwo
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin Mother
 */
class StateTwo implements StateInterface
{
    use StateTrait;

    public function methodPublic()
    {
        /*
         * @return int
         */
        return function () {
            return 123;
        };
    }

    protected function methodProtected()
    {
        /*
         * @return int
         */
        return function () {
            return 456;
        };
    }

    private function methodPrivate()
    {
        /*
         * @return int
         */
        return function () {
            return 789;
        };
    }

    public function methodRecallPrivate()
    {
        /*
         * @return int
         */
        return function () {
            return $this->methodPrivate() * 2;
        };
    }

    public function updateVariable()
    {
        return function ($value) {
            $this->motherVariable = $value;
        };
    }

    public function getMotherVariable()
    {
        return function () {
            return $this->motherVariable;
        };
    }
}
