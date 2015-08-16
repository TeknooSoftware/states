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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * Mock factory file to test command for cli helper
 */

namespace Acme\GoodFactory;

use UniAlteri\States\Factory\Exception;
use UniAlteri\States\Factory\FactoryInterface;
use UniAlteri\States\Loader\FinderInterface;
use UniAlteri\States\Proxy\ProxyInterface;

class Factory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\string $statedClassName, FinderInterface $finder, \ArrayAccess $factoryRepository)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getFinder(): FinderInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startup(ProxyInterface $proxyObject, \string $stateName = null): FactoryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build($arguments = null, \string $stateName = null): ProxyInterface
    {
    }
}
