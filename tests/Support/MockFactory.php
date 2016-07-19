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
 */
namespace Teknoo\Tests\Support;

use Teknoo\States\Factory\FactoryInterface;
use Teknoo\States\Loader\FinderInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class MockFactory
 * Mock factory to tests proxies and loaders. Logs only all actions.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class MockFactory implements FactoryInterface
{
    /**
     * To list initialized factory by loader.
     *
     * @var string[]
     */
    protected static $initializedFactoryNameArray = array();

    /**
     * @var ProxyInterface
     */
    protected $startupProxy;

    /**
     * @var string
     */
    protected $statedClassName = null;

    /**
     * @var string
     */
    protected $path = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $statedClassName, FinderInterface $finder, \ArrayAccess $factoryRepository)
    {
        $this->initialize($statedClassName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinder(): FinderInterface
    {
        //Build a new mock finder
        return new MockFinder($this->statedClassName, $this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatedClassName(): string
    {
        return $this->statedClassName;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(string $statedClassName): FactoryInterface
    {
        $this->statedClassName = $statedClassName;
        self::$initializedFactoryNameArray[] = $statedClassName;

        return $this;
    }

    /**
     * Method added for tests to get action logs
     * Return the list of initialized factories by the loader.
     *
     * @return string[]
     */
    public static function resetInitializedFactories()
    {
        self::$initializedFactoryNameArray = array();
    }

    /**
     * Method added for tests to get action logs
     * Return the list of initialized factories by the loader.
     *
     * @return string[]
     */
    public static function listInitializedFactories()
    {
        return array_values(self::$initializedFactoryNameArray);
    }

    /**
     * {@inheritdoc}
     */
    public function build($arguments = null, string $stateName = null): ProxyInterface
    {
        return new MockProxy(array());
    }

    /**
     * {@inheritdoc}
     */
    public function startup(ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        $this->startupProxy = $proxyObject;

        return $this;
    }

    /**
     * Get the proxy called to startup it
     * Method added for tests to check startup behavior.
     *
     * @return ProxyInterface
     */
    public function getStartupProxy(): ProxyInterface
    {
        return $this->startupProxy;
    }
}
