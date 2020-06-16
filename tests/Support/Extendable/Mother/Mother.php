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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support\Extendable\Mother;

use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\Tests\Support\Extendable\Mother\States\StateDefault;
use Teknoo\Tests\Support\Extendable\Mother\States\StateOne;
use Teknoo\Tests\Support\Extendable\Mother\States\StateTwo;

/**
 * Proxy Mother
 * Proxy class of the stated class Mother
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Mother implements ProxyInterface
{
    use ProxyTrait;

    private $motherVariable;

    /**
     * Initialize the proxy by calling the method initializeProxy.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
    }

    protected static function statesListDeclaration(): array
    {
        return [
            StateDefault::class,
            StateOne::class,
            StateTwo::class,
        ];
    }

    /**
     * Return the list of available state in this class.
     *
     * @return array
     */
    public function listMethodsByStates()
    {
        $ignoreMethods = [
            '__construct',
            'getReflectionClass',
            'checkVisibilityPrivate',
            'checkVisibilityProtected',
            'checkVisibilityPublic',
            'checkVisibility',
            'loadMethodDescription',
            'getClosure',
            'executeClosure'
        ];

        $methodsList = array();
        foreach ($this->getStatesList() as $stateName => $stateContainer) {
            $methodsList[$stateName] = [];
            foreach ((new \ReflectionObject($stateContainer))->getMethods() as $method) {
                $methodName = $method->getName();
                if (!\in_array($methodName, $ignoreMethods)) {
                    $methodsList[$stateName][] = $methodName;
                }
            }
        }

        ksort($methodsList);

        return $methodsList;
    }

    /**
     * Return the list of registered states instance. Present only for debug and tests
     */
    public function getStatesList() : array
    {
        if (!empty($this->states)) {
            return $this->states;
        }

        return [];
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

    /**
     * Return the list of enabled states. Present only for debug and tests
     */
    public function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && \is_array($this->activesStates)) {
            return \array_keys($this->activesStates);
        } else {
            return [];
        }
    }

    public function classicGetMotherVariable()
    {
        return $this->motherVariable;
    }
}
