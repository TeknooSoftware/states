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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace Acme\Extendable\GrandDaughter;

use Acme\Extendable\Daughter\Daughter;
use Acme\Extendable\GrandDaughter\States\StateThree;

/**
 * Proxy GrandDaughter
 * Proxy class of the stated class GrandDaughter.
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
class GrandDaughter extends Daughter
{
    protected static function statesListDeclaration(): array
    {
        return [
            StateThree::class
        ];
    }

    /**
     * Return the list of available state in this class.
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
