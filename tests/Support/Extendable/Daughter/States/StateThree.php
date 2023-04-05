<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Extendable\Daughter\States;

use Teknoo\States\State\AbstractState;

/**
 * State StateThree
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin GrandDaughter
 */
class StateThree extends AbstractState
{
    public function method6()
    {
        /*
         * @return int
         */
        return fn(): int => 666;
    }

    public function methodRecallMotherPrivate()
    {
        /*
         * @return int
         */
        return fn(): int|float => $this->methodPrivate() * 3;
    }

    public function methodRecallMotherProtected()
    {
        /*
         * @return int
         */
        return fn(): int|float => $this->methodProtected() * 3;
    }
}
