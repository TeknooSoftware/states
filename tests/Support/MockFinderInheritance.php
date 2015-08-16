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

use UniAlteri\States\DI;
use UniAlteri\States\Loader\Exception;
use UniAlteri\States\Proxy;
use UniAlteri\States\Loader;
use UniAlteri\States\State;

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
class MockFinderInheritance implements Loader\FinderInterface
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
     * List all available state object of the stated class.
     *
     * @return string[]
     */
    public function listStates()
    {
        //Return mock states
        if (empty(static::$ignoreDefaultState)) {
            return array(
                'MockState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
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
     * Load and build the required state object of the stated class.
     *
     * @param string $stateName
     *
     * @return States\StateInterface
     *
     * @throws Exception\UnReadablePath   if the state file is not readable
     * @throws Exception\UnavailableState if the required state is not available
     * @throws Exception\IllegalState     if the state object does not implement the interface
     */
    public function buildState(string $stateName, bool $privateMode, string $statedClassName): States\StateInterface
    {
        //Return a new mock state object for tests
        return new MockState($privateMode, $statedClassName);
    }

    /**
     * Load the required state object of the stated class.
     *
     * @param string $stateName
     *
     * @return \UniAlteri\States\State\StateInterface
     */
    public function loadState(string $stateName): string
    {
        return true;
    }

    /**
     * Load a proxy object for the stated class.
     *
     * @param array $arguments argument for proxy
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\IllegalProxy If the proxy object does not implement Proxy/ProxyInterface
     */
    public function loadProxy($arguments = null): string
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
     * Load and build a proxy object of the stated class.
     *
     * @param array $arguments argument for proxy
     *
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function buildProxy($arguments = null): Proxy\ProxyInterface
    {
        return new MockProxy($arguments);
    }

    /**
     * To return the list of parents stated classes of this stated classes, library classes (Integrated proxy and
     * standard proxy are excluded).
     *
     * @return string[]
     *
     * @throws Exception\IllegalProxy If the proxy class is not valid
     */
    public function listParentsClassesNames()
    {
        return new \ArrayObject(['My\Stated\Class1']);
    }

    /**
     * To get the canonical stated class name associated to this state.
     *
     * @return $this
     */
    public function getStatedClassName(): string
    {
        return $this->statedClassName;
    }
}
