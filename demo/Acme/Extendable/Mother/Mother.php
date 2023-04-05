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
namespace Acme\Extendable\Mother;

use Acme\Extendable\Mother\States\StateDefault;
use Acme\Extendable\Mother\States\StateOne;
use Acme\Extendable\Mother\States\StateTwo;
use Teknoo\States\Proxy;

/**
 * Proxy Mother
 * Proxy class of the stated class Mother.
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
class Mother implements Proxy\ProxyInterface
{
    use Proxy\ProxyTrait;

    public function __construct()
    {
        $this->initializeStateProxy();
    }

    protected static function statesListDeclaration(): array
    {
        return [
            StateDefault::class,
            StateOne::class,
            StateTwo::class
        ];
    }

    /**
     * Return the list of registered states. Present only for debug and tests
     */
    public function listAvailableStates(): array
    {
        if (!empty($this->states) && \is_array($this->states)) {
            return \array_keys($this->states);
        } else {
            return [];
        }
    }
}
