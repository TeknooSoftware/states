<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Extendable\Mother\States;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State StateTwo
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
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
        return fn (): int => 123;
    }

    protected function methodProtected()
    {
        /*
         * @return int
         */
        return fn (): int => 456;
    }

    private function methodPrivate()
    {
        /*
         * @return int
         */
        return fn (): int => 789;
    }

    public function methodRecallPrivate()
    {
        /*
         * @return int
         */
        return fn (): int|float => $this->methodPrivate() * 2;
    }

    public function updateVariable()
    {
        return function ($value): void {
            $this->motherVariable = $value;
        };
    }

    public function getMotherVariable()
    {
        return fn () => $this->motherVariable;
    }
}
