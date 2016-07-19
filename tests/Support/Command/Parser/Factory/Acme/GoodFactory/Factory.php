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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * Mock factory file to test command for cli helper
 */
namespace Acme\GoodFactory;

use Teknoo\States\Factory\FactoryInterface;
use Teknoo\States\Loader\FinderInterface;
use Teknoo\States\Proxy\ProxyInterface;

class Factory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $statedClassName, FinderInterface $finder, \ArrayAccess $factoryRepository)
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
    public function startup(ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build($arguments = null, string $stateName = null): ProxyInterface
    {
    }
}
