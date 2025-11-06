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

namespace Acme\Extendable\Daughter;

use Acme\Extendable\Daughter\States\StateDefault;
use Acme\Extendable\Daughter\States\StateOne;
use Acme\Extendable\Daughter\States\StateThree;
use Acme\Extendable\Mother\Mother;
use Teknoo\States\Attributes\StateClass;

/**
 * Proxy Daughter
 * Proxy class of the stated class Daughter.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[StateClass(StateDefault::class)]
#[StateClass(StateOne::class)]
#[StateClass(StateThree::class)]
class Daughter extends Mother
{
    /**
     * Return the list of available state in this class.
     *
     * @return array<int|string, mixed>
     */
    public function listMethodsByStates(): array
    {
        $methodsList = [];
        foreach ($this->getStatesList() as $stateName => $stateContainer) {
            $methodsList[$stateName] = $stateContainer->listMethods();
        }

        ksort($methodsList);

        return $methodsList;
    }
}
