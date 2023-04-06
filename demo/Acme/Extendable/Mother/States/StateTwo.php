<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace Acme\Extendable\Mother\States;

use Acme\Extendable\Mother\Mother;
use Teknoo\States\State\AbstractState;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State StateTwo.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin Mother
 */
class StateTwo implements StateInterface
{
    use StateTrait;

    public function methodPublic()
    {
        /**
         * @return int
         */
        return fn(): int => 123;
    }

    protected function methodProtected()
    {
        /**
         * @return int
         */
        return fn(): int => 456;
    }

    private function methodPrivate()
    {
        /**
         * @return int
         */
        return fn(): int => 789;
    }

    public function methodRecallPrivate()
    {
        /**
         * @return int
         */
        return fn(): int|float => $this->methodPrivate() * 2;
    }
}
