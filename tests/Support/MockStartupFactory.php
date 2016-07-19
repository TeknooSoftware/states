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
use Teknoo\States\Factory\StartupFactoryInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class MockStartupFactory
 * Mock startup factory to test integrated proxy behavior.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class MockStartupFactory implements StartupFactoryInterface
{
    /**
     * Proxy to initialize called with forwardStartup
     * Public to allow testCase to check its value to confirm the behavior of the integrated proxy.
     *
     * @var ProxyInterface
     */
    public static $calledProxyObject = null;

    /**
     * {@inheritdoc}
     */
    public static function forwardStartup(ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        self::$calledProxyObject = $proxyObject;

        return new MockFactory(
            'my\Stated\Class',
            new MockFinder('my\Stated\Class', 'path\to\stated\class'),
            new \ArrayObject([])
        );
    }
}
