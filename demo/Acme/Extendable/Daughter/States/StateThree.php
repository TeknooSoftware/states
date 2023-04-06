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
namespace Acme\Extendable\Daughter\States;

use Acme\Extendable\GrandDaughter\GrandDaughter;
use Teknoo\States\State\AbstractState;

/**
 * State StateThree.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin GrandDaughter
 */
class StateThree extends AbstractState
{
    public function method6()
    {
        /**
         * @return int
         */
        return fn(): int => 666;
    }

    public function methodRecallMotherPrivate()
    {
        /**
         * @return int
         */
        return fn(): int|float => $this->methodPrivate() * 3;
    }

    public function methodRecallMotherProtected()
    {
        /**
         * @return int
         */
        return fn(): int|float => $this->methodProtected() * 3;
    }
}
