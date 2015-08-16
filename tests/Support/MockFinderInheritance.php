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
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\Loader\Exception;
use UniAlteri\States\Proxy\ProxyInterface;
use UniAlteri\States\Loader\FinderInterface;
use UniAlteri\States\State\StateInterface;

/**
 * Class MockFinder
 * Mock finder to test behavior of proxies and factories.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockFinderInheritance implements FinderInterface
{
    /**
     * To not return the default state.
     *
     * @var bool
     */
    public static $ignoreDefaultState = false;

    /**
     * To test if the proxy has been loaded by the factory.
     *
     * @var bool
     */
    protected $proxyLoaded = false;

    /**
     * @var string
     */
    protected $statedClassName;

    /**
     * @var string
     */
    protected $pathString;

    /**
     * Initialize finder.
     *
     * @param string $statedClassName
     * @param string $pathString
     */
    public function __construct($statedClassName, $pathString)
    {
        $this->statedClassName = $statedClassName;
        $this->pathString = $pathString;
    }

    /**
     * {@inheritdoc}
     */
    public function listStates()
    {
        //Return mock states
        if (empty(static::$ignoreDefaultState)) {
            return array(
                'MockState1',
                ProxyInterface::DEFAULT_STATE_NAME,
                'MockState4',
            );
        } else {
            return array(
                'MockState1',
                'MockState4',
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildState(\string $stateName, \bool $privateMode, \string $statedClassName): StateInterface
    {
        //Return a new mock state object for tests
        return new MockState($privateMode, $statedClassName);
    }

    /**
     * {@inheritdoc}
     */
    public function loadState(\string $stateName): \string
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function loadProxy($arguments = null): \string
    {
        $this->proxyLoaded = true;

        return true;
    }

    /**
     * To test if the proxy has been loaded by the proxy
     * Method added for tests to check factory behavior.
     *
     * @return bool
     */
    public function proxyHasBeenLoaded()
    {
        return $this->proxyLoaded;
    }

    /**
     * {@inheritdoc}
     */
    public function buildProxy($arguments = null): ProxyInterface
    {
        return new MockProxy($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function listParentsClassesNames()
    {
        return new \ArrayObject(['My\Stated\Class1']);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): \string
    {
        return $this->statedClassName;
    }
}
