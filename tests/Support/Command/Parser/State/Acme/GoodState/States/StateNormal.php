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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * Mock factory file to test command for cli helper
 */

namespace Acme\GoodState\States;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;
use UniAlteri\States\State\Exception;
use UniAlteri\States\State\StateInterface;

class StateNormal implements StateInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\bool $privateMode, \string $statedClassName, array $aliases=[])
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): \string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setStatedClassName(\string $statedClassName): StateInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setStateAliases(array $aliases): StateInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getStateAliases()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isPrivateMode(): \bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setPrivateMode(\bool $enable): StateInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function listMethods()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function testMethod(
        \string $methodName,
        \string $scope = self::VISIBILITY_PUBLIC,
        \string $statedClassOriginName = null
    ): bool {
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodDescription(\string $methodName): \ReflectionMethod
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getClosure(
        \string $methodName,
        \string $scope = self::VISIBILITY_PUBLIC,
        \string $statedClassOriginName = null
    ): \Closure {
    }
}
